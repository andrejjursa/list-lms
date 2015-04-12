import os, os.path, sys, hashlib, base64

class LISTTestScoring:
	__encryptPhrase = None
	__scoreTable = {}

	def __init__(self):
		if not self.__loadEncryptPhrase():
			print('Can\'t found pre-generated encryption phrase source file. Terminating test execution.')
			sys.exit(101)
		if not self.__deleteEncryptPhrase():
			print('Can\'t delete pre-generated encryption phrase source file. Terminating test execution.')
			sys.exit(102)

	def __loadEncryptPhrase(self):
		try:
			epFile = open('__list_encrypt_phrase.txt', 'r')
			phrase = epFile.readline()
			epFile.close()
			if len(phrase) == 8192:
				self.__encryptPhrase = phrase
				return True
			return False
		except (IOError, TypeError):
			return False

	def __deleteEncryptPhrase(self):
		if os.path.isfile('__list_encrypt_phrase.txt'):
			os.remove('__list_encrypt_phrase.txt')
			return not os.path.isfile('__list_encrypt_phrase.txt')
		return False

	def updateScore(self, scoreName, scoreToAdd, scoreMaximum = None):
		if scoreMaximum is not None:
			if scoreName in self.__scoreTable:
				self.__scoreTable[scoreName].setMaximum(scoreMaximum)
				self.__scoreTable[scoreName].addCurrent(scoreToAdd)
			else:
				score = ScoreItem(scoreToAdd, scoreMaximum)
				self.__scoreTable[scoreName] = score
		else:
			if scoreName in self.__scoreTable:
				self.__scoreTable[scoreName].addCurrent(scoreToAdd)
			else:
				print('Scoring error, you are trying to modify non-existing score name: ' + scoreName)		
		self.__writeScore()

	def setScore(self, scoreName, scoreToSet, scoreMaximum = None):
		if scoreMaximum is not None:
			if scoreName in self.__scoreTable:
				del self.__scoreTable[scoreName]
			score = ScoreItem(scoreToSet, scoreMaximum)
			self.__scoreTable[scoreName] = score
		else:
			if scoreName in self.__scoreTable:
				self.__scoreTable[scoreName].setCurrent(scoreToSet)
			else:
				print('Scoring error, you are trying to modify non-existing score name: ' + scoreName)
		self.__writeScore()

	def __getJSONscoring(self):
		items = list()
		for scoreName in self.__scoreTable:
			score = self.__scoreTable[scoreName]
			items.append(''.join(('{', '"name":"', self.__fixWrongChars(scoreName), '","current":', str(score.getCurrent()),',"maximum":', str(score.getMaximum()), '}')))
		return ''.join(('[', ','.join(items), ']'))

	def __fixWrongChars(self, text):
		return text.replace('\\', '\\\\').replace('"', '\\"')

	def __getMD5hash(self, text):
		md5 = hashlib.md5();
		md5.update(text.encode('utf-8'))
		return md5.hexdigest()

	def __encode(self, text):
		md5 = base64.b64encode(bytes(self.__encodeSingleLine(self.__getMD5hash(text)), 'utf-8'))
		json = base64.b64encode(bytes(self.__encodeSingleLine(text, 32), 'utf-8'))
		return ''.join((md5.decode('utf-8'), '\n', json.decode('utf-8')))

	def __encodeSingleLine(self, text, offset = 0):
		chars = list()
		for i in range(0, len(text), 1):
			b = ord(text[i]) ^ ord(self.__encryptPhrase[offset + i % len(self.__encryptPhrase)])
			chars.append(chr(b))
		return ''.join(chars)

	def __writeScore(self):
		try:
			f = open('__list_score.txt', 'w')
			f.write(self.__encode(self.__getJSONscoring()))
			f.close()
		except IOError:
			pass
		

class ScoreItem:
	__current = 0.0
	__maximum = 0.0
	
	def __init__(self, current, maximum):
		self.__current = current
		self.__maximum = maximum
		if self.__maximum < 0.0:
			self.__maximum = 0.0
		if self.__current < 0.0:
			self.__current = 0.0
		elif self.__current > self.__maximum:
			self.__current = self.__maximum

	def getCurrent(self):
		return self.__current

	def getMaximum(self):
		return self.__maximum

	def setCurrent(self, current):
		self.__current = current
		if self.__current < 0.0:
			self.__current = 0.0
		elif self.__current > self.__maximum:
			self.__current = self.__maximum

	def setMaximum(self, maximum):
		self.__maximum = maximum
		if self.__maximum < 0.0:
			self.__maximum = 0.0
		if self.__current > self.__maximum:
			self.__current = self.__maximum

	def addCurrent(self, add):
		self.setCurrent(self.getCurrent() + add)

	def __str__(self):
		return '{0} / {1}'.format(self.__current, self.__maximum)

