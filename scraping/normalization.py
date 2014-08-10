import re
from unidecode import unidecode

def normalize(str):
	return re.sub("[^a-z]", "", unidecode(str.lower()))
