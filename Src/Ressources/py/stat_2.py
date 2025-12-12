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

imagePath = "/var/www/rpi11/Ressources/image_stat/stat2.webp"
imagePathTest = "Downloads/rpi11/Ressources/image_stat/stat2.webp" #Pour test local

try:

    conn = mariadb.connect(**BD)
    query = "SELECT duration_seconds FROM Connections;" 

    df = pandas.read_sql(query, conn)

    # On recupère la durée de connexion en seconde dans la table
    df['duration_seconds'] = pandas.to_numeric(df['duration_seconds'], errors='coerce')
    # Convertion en minute
    df['duration_minutes'] = (df['duration_seconds']/60)

    # On en fait une moyenne
    moyenne_connexion = df['duration_minutes'].mean()

    # Separation 0-10 mn , 10-30mn, 30-60mn, 60-9999mn
    separation = [0,10,30,60,9999]

    # Les labels
    label = ["Très courte (<10 min)", "Courte (10-30m)", "Moyenne (30-60m)", "Longue (> 1h)"]

    # On decoupe les minutes avec nos separation pour les mettre sous forme de categorie
    df['categorie'] = pandas.cut(df['duration_minutes'], bins=separation, labels=label)

    # On compte le nombre de connexion qui sont dans la tranche de la categorie
    nb = df['categorie'].value_counts(sort=False)

    # On en fait un plot (diagrame barre) 
    nb.plot(kind='bar', color = 'blue')

    # Dessin du plot 
    plt.title("Répartititon des temps de connexion", fontweight="bold")
    plt.xlabel(f"Le temps de connexion moyen est de {moyenne_connexion:.0f} minutes.", fontsize = 12, fontweight="bold")
    plt.xticks(rotation=0)
    plt.tight_layout()
    plt.savefig(imagePath)
    plt.savefig(imagePathTest)

    print("Image enregistrée")
    
except mariadb.Error as e:
    print(f"ERREUR SQL : {e}")
except Exception as e:
    print(f"ERREUR GENERALE : {e}")
finally:
    if 'conn' in locals():
        conn.close()    