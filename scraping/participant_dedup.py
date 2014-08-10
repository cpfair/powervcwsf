from colorama import Fore, Back, Style

class InteractiveParticipantDeduplication:
	def Run(participants, regions, exceptions_file):
		exceptions_file = open(exceptions_file, "a+")

		year = int(input("Year? "))

		# The logic goes:
		# We get duplicate participants when repeat participants change their name year to year
		# So, we first only consider new-this-year participants for being duplicates
		# Then, we'll assume you don't change both your name and your region in a year
		# So, only compare each potential duplicate against people in their region
		# Then, since you can't go >6 years, don't offer people who started their careers >=6 years ago as options
		# Since comparisons are done per-region, we iterate by those instead of the potential duplicates

		potential_duplicates = [x for x in participants if len(x.Participations) == 1 and x.Participations[0].Year == year]
		print("Considering %d potential duplicates" % len(potential_duplicates))

		regions = sorted(regions, key=lambda x: x.Name)

		regidx = input("Skip to region idx? ")
		if not regidx:
			regidx = 0
		else:
			regidx = int(regidx) # 71

		print("Wheee...")

		runtime_merges = []

		while regidx < len(regions):

			region = regions[regidx]
			participant_intersect = [x for x in potential_duplicates if x.Participations[0].Project.Region == region]
			if len(participant_intersect) == 0:
				# Nothing to do here
				regidx += 1
				continue

			show_again = True
			while show_again:
				show_again = False
				print(chr(27) + "[2J")
				print("(%d) %s" % (regidx, region.Name))
				eligible_participants = sorted([x for x in participants if x.Participations[0].Year > year - 6 and region in [y.Project.Region for y in x.Participations]], key=lambda x: x.Name)
				participant_intersect = participant_intersect
				for partidx in range(len(eligible_participants)):
					participant = eligible_participants[partidx]
					if participant.Name in runtime_merges:
						continue # Pretend like we merged them
					print("%02d%s %s (%s)" % (partidx, Fore.WHITE + Back.BLACK if participant in participant_intersect else "", participant.Name, participant.NormalizedName) + Style.RESET_ALL)

				while True:
					command = input()
					if command == "b":
						# Back it up
						regidx -= 2
						break
					if command == "":
						# Finished
						break
					if len(command.split(" ")) > 1:
						merge_participants = [eligible_participants[int(x)] for x in command.split(" ")]
						# Merge later into earlier
						merge_participants = sorted(merge_participants, key=lambda x: x.Participations[0].Year)
						for x in range(1, len(merge_participants)):
							exceptions_file.write("%s\t%s\n" % (merge_participants[0].NormalizedName, merge_participants[x].Name))
							runtime_merges.append(merge_participants[x].Name)
						exceptions_file.flush()
						show_again = True
						break

			regidx += 1



