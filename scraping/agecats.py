class AgeCat:
	def __init__(self, code, name, gradeStart, gradeEnd):
		self.Code = code
		self.Name = name
		self.GradeStart = gradeStart
		self.GradeEnd = gradeEnd

class AgeCats:
	def __init__(self):
		self._cats = {
			"Junior": AgeCat(1, "Junior", 7, 8),
			"Intermediate": AgeCat(2, "Intermediate", 9, 10),
			"Senior": AgeCat(3, "Senior", 11, 12)
		}

	def Find(self, name):
		return self._cats[name]