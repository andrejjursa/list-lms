import random
import LISTTestScoring 

def randomString():
	chars = list()
	alphabet = list('ABCDEFGHIJKLMNOPRSQTUVWXYZ0123456789')
	print(alphabet)
	for i in range(0, 8192):
		index = random.randint(0, len(alphabet) - 1)
		chars.append(alphabet[index])
	return ''.join(chars)

f = open('__list_encrypt_phrase.txt', 'w')
phrase = randomString()
f.write(phrase)
f.close()
print(phrase)
print('\n')

l = LISTTestScoring.LISTTestScoring()
l.updateScore('Priklad 1', 5.0, 25.0)
l.updateScore('Priklad 1', 10.0, 25.0)
