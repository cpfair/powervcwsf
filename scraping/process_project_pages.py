from parser import Parser
from sql_export import SQLExporter

parser = Parser()
parser.Parse(["proj_pages"])
SQLExporter("localhost", "root", None, "powervcwsf").Export(parser)