from finalist import Finalist
from participant import Participant
from project import Project
from region import Region
from divisions import Divisions
from provterrs import ProvTerrs

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
        logger.info(pid)
        project = Project(
            pid=pid,
            name=re.search("<hr /><b>([^<]+)</b>", contents).group(1),
            synopsis=re.search("<td valign=\"top\">Abstract:</td><td>(.+?)</td>", contents).group(1),
            year=int(re.search("CWSF (\d\d\d\d)", contents).group(1))
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

        divisions_names = re.finditer("<td>(Challenge|Division):</td><td>(?P<division>[^<]+)</td>", contents)
        for div_name_match in divisions_names:
            div = self._divisions.Find(div_name_match.group("division"))
            div.MarkAsSeenInYear(project.Year)
            project.Divisions.append(div)

        logger.debug("Project: %s (%d)" % (project.Name, project.Year))
        logger.debug("\tRegion: %s (%s)" % (project.Region, project.ProvTerr))
        logger.debug("\tFinalists: %s" % ", ".join([str(x) for x in project.Finalists]))
        logger.debug("\tDivisions: %s" % ", ".join([str(x) for x in project.Divisions]))
        logger.debug("\tSynopsis: %s..." % project.Synopsis[:80])

    def _resolve_participant(self, finalist):
        search_name = self._normalize_name(finalist.Name)
        if finalist.Name in self._name_exceptions:
            search_name = self._name_exceptions[finalist.Name]

        for part in self._participants:
            if part.NormalizedName == search_name:
                part.Participations.append(finalist)
                return part

        new_participant = Participant(finalist)
        new_participant.NormalizedName = search_name
        self._participants.append(new_participant)
        return new_participant

    def _resolve_region(self, name):
        if name not in self._regions:
            self._regions[name] = Region(name)
        return self._regions[name]



    def _normalize_name(self, name):
        return name



