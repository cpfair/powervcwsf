from finalist import Finalist
from participant import Participant
from project import Project
from region import Region
from divisions import Divisions
from provterrs import ProvTerrs
from award import Award, AwardType
from agecats import AgeCats
from normalization import normalize

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
    def Parse(self, proj_pages_dirs):
        # Later takes precedence in this list
        proj_pages = {}
        for dir in proj_pages_dirs:
            files = os.listdir(dir)
            # Filenames are either pid_year_agecat.htm or pid.html
            proj_pages.update({int(re.match("^(\d+)", file).group(1)): dir+"/"+file for file in files})

        for pid, proj_page_path in proj_pages.items():
            self._parse_page(pid, open(proj_page_path, "r").read())

        print("Finished with\n\t%d projects\n\t%d finalists\n\t%d participants\n\t%d regions" % (len(self._projects), len(self._finalists), len(self._participants), len(self._regions.keys())))

    def _parse_page(self, pid, contents):
        contents = contents.encode("utf-8").decode("latin-1")
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

        project.Region = self._resolve_region(re.search("<td>Region:</td><td>([^<]+)</td>", contents).group(1))
        project.Region.MarkAsSeenInYear(project.Year)
        project.Region.ProvTerr = project.ProvTerr

        finalist_names = re.search("<h3 style=\"margin-top: 0px;\">([^<]+)</h3>", contents).group(1).split(",")

        for name in finalist_names:
            finalist = Finalist(name.strip(), project=project)
            self._finalists.append(finalist)
            finalist.Participant = self._resolve_participant(finalist)
            project.Finalists.append(finalist)

        if len(project.Finalists) not in [1,2]:
            raise "Invalid number of finalists"

        divisions_names = re.finditer("<td>(Challenge|Division):</td><td>(?P<division>[^<]+)</td>", contents)
        for div_name_match in divisions_names:
            div = self._divisions.Find(div_name_match.group("division"))
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
            award = Award(
                name = award_match.group("title"),
                description = award_match.group("description"),
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
