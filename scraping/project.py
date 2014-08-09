class Project:
	def __init__(self, pid, name, synopsis, year, agecat, region=None, provterr=None, divisions=None, finalists=None, awards=None):
		self.ID = pid
		self.Name = name
		self.Synopsis = synopsis
		self.Year = year
		self.AgeCat = agecat
		self.Region = region
		self.ProvTerr = provterr
		self.Divisions = divisions if divisions else []
		self.Finalists = finalists if finalists else []
		self.Awards = awards if awards else []

	@property
	def Winnings(self):
		return sum([x.Value if x.Value else 0 for x in self.Awards])
