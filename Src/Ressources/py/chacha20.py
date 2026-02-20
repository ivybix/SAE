
from Crypto.Cipher import ChaCha20 # Importation de chacha20
from Crypto.Random import get_random_bytes # Génère des clés sécurisées
from json import load
import base64


cle = load(open('../JSON/config.json','r'))

cle =  bytes.fromhex(cle['cle'])

def cryptage(message):
    nonce = base64.b64decode("8XtzaMCtLQsYb/bi") # Generation d'un nonce de 96 bits
    message = message.encode('utf-8') # Encodage du message en UTF-8 (pour avoir des octets)

    cipher = ChaCha20.new(key=cle, nonce=nonce) # Création du cipher, on initialtise la matrice avec la clé et le nonce
    message_chiffre = cipher.encrypt(message) # Chiffrement du message,on génère le keystream et fait le XOR
    return {
            "message": base64.b64encode(message_chiffre).decode('utf-8'),
            "nonce": base64.b64encode(nonce).decode('utf-8')
        }

message = "Salut je m'appelle Samy et j'écris un texte assez long pour tester le cryptage avec ChaCha20 !" # ici mon message à encrypter
print(cryptage("adminweb"))

def decrypt(message_chiffre, nonce):
    # Déchiffrement du message
    cipher_decrypt = ChaCha20.new(key=cle, nonce=nonce) # Création d'un nouveau cipher pour réinitialiser le compteur avec la même clé et le même nonce
    message_dechiffre = cipher_decrypt.decrypt(message_chiffre) # Déchiffrement du message
    return message_dechiffre.decode('utf-8')
