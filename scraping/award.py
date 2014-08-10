class AwardType: # Magic enum values carried from C#
    Unknown = 0
    Cash = 1
    Scholarship = 2
    Other = 3

class Award:
    def __init__(self, name, description, value, type):
        self.Name = name
        self.Description = description
        self.Value = value
        self.Type = type

    def __str__(self):
    	return "%s ($%s %s)" % (self.Name, self.Value, {1:"cash", 2:"scholarship", 3:"other"}[self.Type])
