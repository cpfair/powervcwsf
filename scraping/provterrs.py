class ProvTerr:
    def __init__(self, name, code):
        self.Name = name
        self.Code = code

    def __str__(self):
        return self.Code

class ProvTerrs:
    def __init__(self):
        self._provterrs = []
        self._provterrs.append(ProvTerr("Alberta", "AB"))
        self._provterrs.append(ProvTerr("British Columbia", "BC"))
        self._provterrs.append(ProvTerr("Ontario", "ON"))
        self._provterrs.append(ProvTerr("Quebec", "QC"))
        self._provterrs.append(ProvTerr("Manitoba", "MB"))
        self._provterrs.append(ProvTerr("Saskatchewan", "SK"))
        self._provterrs.append(ProvTerr("New Brunswick", "NB"))
        self._provterrs.append(ProvTerr("Newfoundland", "NL"))
        self._provterrs.append(ProvTerr("Nova Scotia", "NS"))
        self._provterrs.append(ProvTerr("Prince Edward Island", "PE"))
        self._provterrs.append(ProvTerr("Nunavut", "NU"))
        self._provterrs.append(ProvTerr("North West Territories", "NT"))
        self._provterrs.append(ProvTerr("Yukon", "YT"))
        self._provterrs = {x.Code:x for x in self._provterrs}

    def FindByCode(self, code):
        return self._provterrs[code]