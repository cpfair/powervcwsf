from parser import Parser
from sql_export import SQLExporter
from grade_calculator import GradeCalculator

parser = Parser()
parser.Parse(["proj_pages"])

GradeCalculator.CalculateForParticipants(parser.Participants)

SQLExporter("localhost", "root", None, "powervcwsf").Export(parser)