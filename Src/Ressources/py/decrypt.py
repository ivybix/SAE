import sys
import base64
from Crypto.Cipher import ChaCha20
from json import load

# Clé

cle = load(open('/var/www/rpi11/Ressources/JSON/config.json','r'))
cle = bytes.fromhex(cle['cle'])

if len(sys.argv) != 3:
    print("Usage: python decrypt.py <message_chiffré_b64> <nonce_b64>")
    sys.exit(1)

message = base64.b64decode(sys.argv[1])
nonce = base64.b64decode(sys.argv[2])

cipher = ChaCha20.new(key=cle, nonce=nonce)
message_dechiffre = cipher.decrypt(message)

print(message_dechiffre.decode("utf-8"))
