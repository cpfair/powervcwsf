import sys
import requests
import re
import os

years = [int(x) for x in sys.argv[1:]]

if not os.path.exists("proj_pages"):
	os.mkdir("proj_pages")

project_count = 0

for year in years:
	print("Year %d" % year)
	for age_cat in range(1,4):
		print("\tAge cat %d" % age_cat)
		listing_page_resp = requests.get("https://secure.youthscience.ca/virtualcwsf/browse.php", params={"year": year, "categoryid": age_cat})
		project_ids = re.findall("projectdetails\.php\?id=(?P<pid>\d+)", listing_page_resp.text)
		for pid in project_ids:
			pid = int(pid)
			sys.stdout.write("\t\t%d... " % pid)
			filename = "proj_pages/%d_%d_%d.htm" % (pid, year, age_cat)
			if not os.path.exists(filename):
				page_resp = requests.get("https://secure.youthscience.ca/virtualcwsf/projectdetails.php", params={"id": pid})
				open(filename, "wb").write(page_resp.content)
				print("downloaded")
			else:
				print("cached")

		project_count += len(project_ids)

print("Ensured %d project pages" % project_count)
