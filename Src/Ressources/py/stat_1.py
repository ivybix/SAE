import mariadb
import pandas
import matplotlib.pyplot as plt
import sys


BD = {
    'user' : 'inkware',
    'password': '!sae2025!',
    'host': 'localhost',
    'database': 'INVENTORY'
}

# On ecrase l'image que le site récupere a l'affichage
imagePath = "/var/www/rpi11/Ressources/image_stat/stat1.webp"
imagePathTest = "Downloads/rpi11/Ressources/image_stat/stat2.webp" #Pour test local

try:

    # Connexion à la base
    conn = mariadb.connect(**BD)
    query = "SELECT PURCHASE_DATE FROM Devices;" 

    # On crée un dataframe avec toute les valeurs de la requête
    df = pandas.read_sql(query, conn)

    # On recupere la date d'aujourd'hui
    today_date = pandas.Timestamp.now().normalize()

    # On recepere l'age  (date aujd - date d'achat = temps écoulé)
    df['age_jour'] = (today_date-(pandas.to_datetime(df['PURCHASE_DATE'])))

    # Conversion en années
    df['age_annee'] = (df['age_jour'].dt.days/365.25)

    # On divise notre camembert en 4 partie : 0-1 an, 1-3 ans, 3-5 ans, 5-20 ans 
    separation = [0,1,3,5,20]

    # On labelise
    label = ["0-1 année", "1-3 annnées", "3-5 années", "5-20 années"]

    # On decoupe les valeurs en fonction de la separation sous les label (EX: GroupBy)
    df['categorie'] = pandas.cut(df['age_annee'], bins=separation, labels=label)
     
    # Nombre de pc de cette categorie
    nb = df['categorie'].value_counts(sort=False)

    # Dessin du camembert
    nb.plot(kind='pie', autopct='%1.0f%%', ylabel='')
    plt.title("Répartititon des machines en fonction de leur ancienneté", fontweight = "bold")
    plt.xticks(rotation=0)
    plt.tight_layout()
    plt.savefig(imagePath)

    print("Image enregistrée")
    
except mariadb.Error as e:
    print(f"ERREUR SQL : {e}")
except Exception as e:
    print(f"ERREUR GENERALE : {e}")
finally:
    if 'conn' in locals():
        conn.close()    