import re

def normalize(str):
	return re.sub("[^a-z]", "", str.lower())
