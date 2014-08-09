# I don't feel like messing with an ORM and getting it to fit the existing data model today.
import pymysql
from award import AwardType
import json

class SQLExporter:
    def __init__(self, host, user, password, db):
        self._conn = pymysql.connect(host=host, user=user, passwd=password, db=db)

    def Export(self, parser):
        self._export_divisions(parser)
        self._export_regions(parser)
        self._export_projects(parser)
        self._export_participants(parser)

    def _export_divisions(self, parser):
        cursor = self._conn.cursor()
        cursor.execute("TRUNCATE TABLE `divisions`")
        # Blegh, I just want to finish this at this point, I don't care about accessing private members
        for division in parser.Divisions:
            cursor.execute("""
                INSERT INTO `divisions`
                (`NormalizedName`,`Name`,`FirstSeenYear`,`LastSeenYear`)
                VALUES
                (%(nname)s,%(name)s,%(fsy)s,%(lsy)s)
                """,
                {
                    "nname": division.NormalizedName,
                    "name": division.Name,
                    "fsy": division.FirstSeenYear,
                    "lsy": division.LastSeenYear
                })
        self._conn.commit()

    def _export_regions(self, parser):
        cursor = self._conn.cursor()
        cursor.execute("TRUNCATE TABLE `regions`")
        for region in parser.Regions:
            cursor.execute("""
                INSERT INTO `regions`
                (`Name`,`NormalizedName`,`ProvTerr`,`FirstSeenYear`,`LastSeenYear`)
                VALUES
                (%(name)s,%(normname)s,%(provterr)s,%(firstyear)s,%(lastyear)s)
                """,
                {
                    "name": region.Name,
                    "normname": region.NormalizedName,
                    "provterr": region.ProvTerr.Code if region.ProvTerr else None,
                    "firstyear": region.FirstSeenYear,
                    "lastyear": region.LastSeenYear
                })
        self._conn.commit()

    def _export_participants(self, parser):
        cursor = self._conn.cursor()
        cursor.execute("TRUNCATE TABLE `participants`")

        for participant in parser.Participants:
            cursor.execute("""
                INSERT INTO `participants`
                (
                    `NormalizedName`,
                    `Name`,
                    `GradeAnchor`,
                    `Winnings`,
                    `UndividedWinnings`
                ) VALUES (
                    %(nname)s,
                    %(name)s,
                    %(ga)s,
                    %(winnings)s,
                    %(undivwinnings)s
                )""",
                {
                    "nname": participant.NormalizedName,
                    "name": participant.Name,
                    "ga": participant.GradeAnchor,
                    "winnings": participant.DividedWinnings,
                    "undivwinnings": participant.UndividedWinnings
                })
        self._conn.commit()
    def _export_projects(self, parser):
        cursor = self._conn.cursor()
        cursor.execute("TRUNCATE TABLE `projects`")

        for project in parser.Projects:
            cursor.execute("""
                INSERT INTO `projects`
                    (
                        `RegID`,
                        `Year`,
                        `Name`,
                        `ParticipantA`,
                        `ParticipantB`,
                        `DivisionA`,
                        `DivisionB`,
                        `AgeCat`,
                        `ProvTerr`,
                        `Region`,
                        `Synopsis`,
                        `CashAwardsValue`,
                        `ScholarshipAwardsValue`,
                        `OtherAwardsValue`,
                        `Awards`,
                        `AwardsData`
                    ) VALUES (
                        %(regid)s,
                        %(year)s,
                        %(name)s,
                        %(finalista)s,
                        %(finalistb)s,
                        %(diva)s,
                        %(divb)s,
                        %(agecat)s,
                        %(provterr)s,
                        %(region)s,
                        %(synopsis)s,
                        %(cashval)s,
                        %(scholval)s,
                        %(otherval)s,
                        %(awards)s,
                        %(awardsdat)s
                    )""",
                {
                    "regid": project.ID,
                    "year": project.Year,
                    "name": project.Name,
                    "finalista": project.Finalists[0].Participant.NormalizedName if len(project.Finalists) > 0 and project.Finalists[0].Participant.NormalizedName else None,
                    "finalistb": project.Finalists[1].Participant.NormalizedName if len(project.Finalists) > 1 and project.Finalists[1].Participant.NormalizedName else None,
                    "diva": project.Divisions[0].NormalizedName,
                    "divb": project.Divisions[1].NormalizedName if len(project.Divisions) > 1 else None,
                    "agecat": project.AgeCat.Code,
                    "provterr": project.ProvTerr.Code if project.ProvTerr else None,
                    "region": project.Region.NormalizedName,
                    "synopsis": project.Synopsis,
                    "cashval": sum([x.Value if x.Value else 0 for x in project.Awards if x.Type == AwardType.Cash]),
                    "scholval": sum([x.Value if x.Value else 0 for x in project.Awards if x.Type == AwardType.Scholarship]),
                    "otherval": sum([x.Value if x.Value else 0 for x in project.Awards if x.Type == AwardType.Other]),
                    "awards": "\t".join([x.Name for x in project.Awards]),
                    "awardsdat": json.dumps([{"Name": x.Name, "Description": x.Description, "Value": x.Value, "Type": x.Type} for x in project.Awards])
                })
        self._conn.commit()

