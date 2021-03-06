from finalist import Finalist
from participant import Participant
from project import Project
from region import Region
from divisions import Divisions
from provterrs import ProvTerrs
from award import Award, AwardType
from agecats import AgeCats
from normalization import normalize
import unicodedata

import os
import re
import logging
logger = logging.getLogger("vcwsfscrape")
logger.setLevel(logging.DEBUG)
ch = logging.StreamHandler()
ch.setLevel(logging.DEBUG)
logger.addHandler(ch)

class Parser:
    def __init__(self):
        self._projects = []
        self._participants = []
        self._finalists = []
        self._regions = {}
        self._name_exceptions = {}
        self._divisions = Divisions()
        self._provterrs = ProvTerrs()
        self._awardsParser = AwardsParser()
        self._agecats = AgeCats()

    @property
    def Participants(self):
        return self._participants

    @property
    def Finalists(self):
        return self._finalists

    @property
    def Regions(self):
        return self._regions.values()

    @property
    def Divisions(self):
        return self._divisions._divisions

    @property
    def Projects(self):
        return self._projects

    def Parse(self, proj_pages_dirs, name_exception_file):
        self._load_name_exceptions(name_exception_file)
        # Later takes precedence in this list
        proj_pages = {}
        for dir in proj_pages_dirs:
            files = os.listdir(dir)
            for file in files:
                pid = int(re.match("^(\d+)", file).group(1))
                if pid not in proj_pages:
                    proj_pages[pid] = []
                proj_pages[pid].append(dir + "/" + file)

        for pid, proj_page_paths in proj_pages.items():
            for path in proj_page_paths:
                contents = open(path, "r").read()
                if "Project ID missing" in contents or "Invalid project" in contents:
                    continue
                else:
                    self._parse_page(pid, contents)
                    break

        print("Finished with\n\t%d projects\n\t%d finalists\n\t%d participants\n\t%d regions" % (len(self._projects), len(self._finalists), len(self._participants), len(self._regions.keys())))

    def _load_name_exceptions(self, path):
        # The weird TSV format is a holdover from C# days when JSON was a pain
        # It's <root normalizedname>\tFull Ambiguous Name
        file = open(path, "r")
        for line in file:
            line = line.strip()
            self._name_exceptions[line.split("\t")[1]] = line.split("\t")[0]

    def _parse_page(self, pid, contents):
        contents = unicodedata.normalize("NFC", contents)
        logger.info(pid)
        project = Project(
            pid=pid,
            name=re.search("<hr /><b>([^<]+)</b>", contents).group(1),
            synopsis=re.search("<td valign=\"top\">Abstract:</td><td>(.+?)</td>", contents).group(1),
            year=int(re.search("CWSF (\d\d\d\d)", contents).group(1)),
            agecat=self._agecats.Find(re.search("<td>Category:</td><td>([^<]+)</td>", contents).group(1))
        )
        self._projects.append(project)

        provterrcodematch = re.search("<td>City:</td><td>([^<]+\s(\w\w))</td>", contents)
        if provterrcodematch:
            project.ProvTerr = self._provterrs.FindByCode(provterrcodematch.group(2))

        region_match = re.search("<td>Region:</td><td>([^<]+)</td>", contents)

        if region_match:
            project.Region = self._resolve_region(region_match.group(1))
        else:
            project.Region = self._resolve_region("Unknown")

        project.Region.MarkAsSeenInYear(project.Year)
        project.Region.ProvTerr = project.ProvTerr

        finalist_names = []
        finalist_names_match = re.search("<h3 style=\"margin-top: 0px;\">([^<]+)</h3>", contents)
        if finalist_names_match:
            finalist_names = finalist_names_match.group(1).split(",")

        for name in finalist_names:
            finalist = Finalist(name.strip(), project=project)
            self._finalists.append(finalist)
            finalist.Participant = self._resolve_participant(finalist)
            project.Finalists.append(finalist)

        divisions_names = re.search("<td>(Challenge|Division):</td><td>(?P<division>[^<]+)</td>", contents).group(2).split('/')
        for name in divisions_names:
            name = name.strip()
            if name == "None":
                continue
            div = self._divisions.Find(name)
            div.MarkAsSeenInYear(project.Year)
            project.Divisions.append(div)

        project.Awards = self._awardsParser.ParseAwards(contents)

        logger.debug("Project: %s (%d)" % (project.Name, project.Year))
        logger.debug("\tRegion: %s (%s)" % (project.Region, project.ProvTerr))
        logger.debug("\tFinalists: %s" % ", ".join([str(x) for x in project.Finalists]))
        logger.debug("\tDivisions: %s" % ", ".join([str(x) for x in project.Divisions]))
        logger.debug("\tSynopsis: %s..." % project.Synopsis[:80])
        logger.debug("\tAwards:")
        for award in project.Awards:
            logger.debug("\t\t%s" % str(award))

    def _resolve_participant(self, finalist):
        search_name = normalize(finalist.Name)
        if finalist.Name in self._name_exceptions:
            search_name = self._name_exceptions[finalist.Name]

        for part in self._participants:
            if part.NormalizedName == search_name:
                part.Participations.append(finalist)
                return part

        new_participant = Participant(finalist)
        new_participant.NormalizedName = search_name # To make exceptions work properly.
        self._participants.append(new_participant)
        return new_participant

    def _resolve_region(self, name):
        if name not in self._regions:
            self._regions[name] = Region(name)
        return self._regions[name]

class AwardsParser:
    def __init__(self):
        self._typeKeywords = {
            AwardType.Cash: None, # Fallback
            AwardType.Scholarship: ["scholarship"],
            AwardType.Other: ["wiezmann", "australia", "milset"]
        }

    def ParseAwards(self, contents):
        awards = []
        awards_iter = re.finditer("<tr> <td><b>(?P<title>[^<]+)</b><br />(?P<description>.+?)</td> <td align=\"right\" valign=\"top\"><nobr>\$?(?P<value>[\d\.\s]+)?</nobr></td></tr>", contents)
        for award_match in awards_iter:
            title = award_match.group("title")
            description = award_match.group("description")
            if "Medal" in description and "Excellence Award -" in title:
                # Fix the title to be more useful.
                title = title.split("-")[0] + "- " + \
                        re.search(r"\w+ medal", description, re.IGNORECASE).group(0) + " -" + \
                        title.split("-")[-1]
            award = Award(
                name = title,
                description = description,
                value = float(award_match.group("value").replace(" ", "")) if award_match.group("value") else None,
                type = AwardType.Unknown
            )
            keyword_text = str(award.Name + award.Description).lower()
            for type, keywords in self._typeKeywords.items():
                if not keywords:
                    award.Type = type
                    continue
                for keyword in keywords:
                    if keyword in keyword_text:
                        award.Type = type
                        break
            if not award.Value and award.Type == AwardType.Cash:
                award.Type = AwardType.Other # It's not cash if it has no $ value
            awards.append(award)

        return awards
