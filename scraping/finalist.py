class Finalist:
	def __init__(self, name, participant=None, project=None):
		self.Name = name
		self.Participant = participant
		self.Project = project
		self.Grade = None

	@property
	def Year(self):
		return self.Project.Year

	@property
	def DividedWinnings(self):
		return self.Project.Winnings / len(self.Project.Finalists)

	@property
	def UndividedWinnings(self):
		return self.Project.Winnings

	def __str__(self):
		return self.Name
