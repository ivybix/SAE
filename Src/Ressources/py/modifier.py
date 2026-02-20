
from Crypto.Cipher import ChaCha20 # Importation de chacha20
from Crypto.Random import get_random_bytes # Génère des clés sécurisées
from json import load
import base64
import sys

cle = load(open('/var/www/rpi11/Ressources/JSON/config.json','r'))
if len(sys.argv) != 3:
    print("Usage: python mdofier.py \"mdp\"")
    sys.exit(1)

cle =  bytes.fromhex(cle['cle'])
message = sys.argv[1]
# Décoder le nonce depuis Base64
nonce = base64.b64decode(sys.argv[2])

# Encodage du message
message = message.encode('utf-8')

cipher = ChaCha20.new(key=cle, nonce=nonce)  # Création du cipher, on initialtise la matrice avec la clé et le nonce
message_chiffre = cipher.encrypt(message)  # Chiffrement du message,on génère le keystream et fait le XOR
print(base64.b64encode(message_chiffre).decode('utf-8'))