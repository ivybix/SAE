import secrets
import struct

message = "Salut je m'appelle Nicolas et j'écris un texte assez long pour tester les blocs !"
cle = secrets.token_bytes(32) #Génère une clé de 32 octets
nonce = secrets.token_bytes(12)

base = [0x61707865, 0x3320646e, 0x79622d32, 0x6b206574]

def remplissage_matrice(cle,nonce):
    matrice = 16 * [0]
    for i in range(0,4):
        matrice[i] = base[i]
    compteur1 = 0
    compteur2 = 4
    for i in range(4,12):
            matrice[i] = int.from_bytes(cle[compteur1:compteur2],"little")
            compteur1 = compteur2
            compteur2 +=4
    compteur1 = 0
    compteur2 = 4
    for i in range(13,16):
            matrice[i] = int.from_bytes(nonce[compteur1:compteur2],"little")
            compteur1 = compteur2
            compteur2 +=4
    return matrice

def rotation(x,n):
    return (x << n & 0xffffffff) | (x >> (32 - n))


def quarter_round(x, a, b, c, d):

    x[a] = (x[a] + x[b]) & 0xffffffff # Le & 0xffffffff est obligatoire pour que chaque mots reste à une taille de 32 bits
    x[d] = (x[d] ^ x[a]) & 0xffffffff # L'opérateur ^ fait un XOR
    x[d] = rotation(x[d], 16)

    x[c] = (x[c] + x[d]) & 0xffffffff
    x[b] = (x[b] ^ x[c]) & 0xffffffff
    x[b] = rotation(x[b], 12)

    x[a] = (x[a] + x[b]) & 0xffffffff
    x[d] = (x[d] ^ x[a]) & 0xffffffff
    x[d] = rotation(x[d], 8)

    x[c] = (x[c] + x[d]) & 0xffffffff
    x[b] = (x[b] ^ x[c]) & 0xffffffff
    x[b] = rotation(x[b], 7)


def generer_keystream(matrice_entree,compteur):
    matrice_entree[12] = compteur
    matrice_travail = list(matrice_entree)
    for i in range(10):
        quarter_round(matrice_travail,0,4,8,12)
        quarter_round(matrice_travail,1,5,9,13)  #Valeurs donner dans la documentation
        quarter_round(matrice_travail,2,6,10,14) #Elles correspondent au colonne et au diagonale de la matrice
        quarter_round(matrice_travail,3,7,11,15)
        quarter_round(matrice_travail,0,5,10,15)
        quarter_round(matrice_travail,1,6,11,12)
        quarter_round(matrice_travail,2,7,8,13)
        quarter_round(matrice_travail,3,4,9,14)

    #Addition matrice initiale + matrice qui a subit le melange
    for i in range(16):
        matrice_travail[i] = (matrice_travail[i] + matrice_entree[i]) & 0xffffffff

    keystream = b''
    for i in range(0,16):
        keystream += struct.pack('<I',matrice_travail[i])

    return keystream

def chiffrer(message):
    compteur = 0
    matrice = remplissage_matrice(cle, nonce)
    message_bytes = message.encode('utf-8')
    bout_message_liste = []
    matrice_init = list(matrice)
    for k in range(0, len(message_bytes), 64):
        bout_message = message_bytes[k:k+64]
        keystream = generer_keystream(matrice_init,compteur)
        for i in range(len(bout_message)):
            bout_message_liste.append(bout_message[i] ^ keystream[i])
        compteur +=1
    return bytes(bout_message_liste)


def dechiffrer(message_chiffre):
    compteur = 0
    bout_message_liste = []
    matrice = remplissage_matrice(cle,nonce)
    matrice_init = list(matrice)
    for k in range(0, len(message_chiffre), 64):
        bout_message = message_chiffre[k:k+64]
        keystream = generer_keystream(matrice_init,compteur)
        for i in range(len(bout_message)):
            bout_message_liste.append(bout_message[i] ^ keystream[i])
        compteur += 1
    return bytes(bout_message_liste).decode('utf-8')

message_chiffre = chiffrer(message)
print(message_chiffre)
message_dechiffre = dechiffrer(message_chiffre)
print(message_dechiffre)