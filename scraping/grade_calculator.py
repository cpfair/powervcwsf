class GradeCalculator:
	def CalculateForParticipants(participants):
		for participant in participants:
			lastAgeCat = None
			lastAgeCatYear = 0
			hasCheckpoint = False
			for finalist in participant.Participations:
				if lastAgeCat != finalist.Project.AgeCat and lastAgeCat:
					hasCheckpoint = True
					break
				lastAgeCat = finalist.Project.AgeCat
				lastAgeCatYear = finalist.Project.Year

			if not hasCheckpoint:
				continue # We tried

			for finalist in participant.Participations:
				finalist.Grade = lastAgeCat.GradeEnd + (finalist.Year - lastAgeCatYear)
