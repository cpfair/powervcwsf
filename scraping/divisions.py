import re
class Division:
    def __init__(self, names):
        if not isinstance(names, list):
            names = [names]
        self.Name = names[0]
        self.NormalizedName = re.sub("[^a-z]", "", self.Name.lower())
        self.SearchNames = names
        self.FirstSeenYear = 9999
        self.LastSeenYear = 0

    def MarkAsSeenInYear(self, year):
        self.FirstSeenYear = min(self.FirstSeenYear, year)
        self.LastSeenYear = max(self.LastSeenYear, year)

    def __str__(self):
        return self.Name

class Divisions:
    def __init__(self):
        self._divisions = []
        # All Years
        self._divisions.append(Division("International"))

        # Pre-2011
        self._divisions.append(Division("Engineering & Computing Sciences"))

        self._divisions.append(Division("Physical & Mathematical Sciences"))
        self._divisions.append(Division("Life Sciences"))
        self._divisions.append(Division("Health Sciences"))
        self._divisions.append(Division("Environmental Innovation"))
        self._divisions.append(Division("Engineering"))
        self._divisions.append(Division("Earth & Environmental Sciences"))
        self._divisions.append(Division("Computing & Information Technology"))
        self._divisions.append(Division(["Biotechnology", "Biotechnology & Pharmaceutical Sciences"]))
        self._divisions.append(Division("Automotive"))

        # Post-2011
        self._divisions.append(Division("Discovery"))
        self._divisions.append(Division("Environment"))
        self._divisions.append(Division("Energy"))
        self._divisions.append(Division("Health"))
        self._divisions.append(Division("Information"))
        self._divisions.append(Division("Innovation"))
        self._divisions.append(Division("Resources"))

    def Find(self, name):
        for div in self._divisions:
            if name in div.SearchNames:
                return div
