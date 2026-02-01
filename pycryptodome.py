from Crypto.Cipher import ChaCha20 # Importation de chacha20
from Crypto.Random import get_random_bytes # Génère des clés sécurisées

cle = get_random_bytes(32) # Generation d'une clé de 256 bits
nonce = get_random_bytes(12) # Generation d'un nonce de 96 bits

message = "Salut je m'appelle Samy et j'écris un texte assez long pour tester le cryptage avec ChaCha20 !" # ici mon message à encrypter
message = message.encode('utf-8') # Encodage du message en UTF-8 (pour avoir des octets)
cipher = ChaCha20.new(key=cle, nonce=nonce) # Création du cipher, on initialtise la matrice avec la clé et le nonce
message_chiffre = cipher.encrypt(message) # Chiffrement du message,on génère le keystream et fait le XOR

print(f"Cle (hex)     : {cle.hex()}")
print(f"Nonce (hex)   : {nonce.hex()}")
print(f"Message chiffré (hex) : {message_chiffre.hex()}")

# Déchiffrement du message
cipher_decrypt = ChaCha20.new(key=cle, nonce=nonce) # Création d'un nouveau cipher pour réinitialiser le compteur avec la même clé et le même nonce
message_dechiffre = cipher_decrypt.decrypt(message_chiffre) # Déchiffrement du message

print(f"Message déchiffré (UTF-8) : {message_dechiffre.decode('utf-8')}")
