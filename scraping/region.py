from normalization import normalize

class Region:
    def __init__(self, name, provterr=None):
        self.Name = name
        self.NormalizedName = normalize(name)
        self.ProvTerr = provterr
        self.FirstSeenYear = 9999
        self.LastSeenYear = 0

    def __str__(self):
    	return self.Name

    def MarkAsSeenInYear(self, year):
        self.FirstSeenYear = min(self.FirstSeenYear, year)
        self.LastSeenYear = max(self.LastSeenYear, year)
