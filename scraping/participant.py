# A Participant is created for every Finalist
# Once a Participant exists, new Finalists are added to augment it
class Participant:
	def __init__(self, finalist):
		self.Participations = [finalist]
		self.NormalizedName = None # Gets set by the parser because reasons

	def AddParticipation(self, finalist):
		self.Participations.append(finalist)
		self.Participations = sorted(self.Participations, key=lambda x: x.Year)


	@property
	def DividedWinnings(self):
		return sum([x.DividedWinnings for x in self.Participations])

	@property
	def UndividedWinnings(self):
		return sum([x.UndividedWinnings for x in self.Participations])

	@property
	def GradeAnchor(self):
		# The exact meaning of this field has been lost to the mists of time
		# I'm just copy-pasting from the original C#
		if self.Participations[0].Grade != 0:
			return self.Participations[0].Year - self.Participations[0].Grade
		else:
			return 0

