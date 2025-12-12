import mariadb
import pandas
import matplotlib.pyplot as plt
from matplotlib.gridspec import GridSpec
import sys

BD = {
    'user' : 'inkware',
    'password': '!sae2025!',
    'host': 'localhost',
    'database': 'INVENTORY'
}

imagePath = "/var/www/rpi11/Ressources/image_stat/stat3.webp"
imagePathTest = "Downloads/rpi11/Ressources/image_stat/stat3.webp" #Pour test local

try:

    # Connexion à la base
    conn = mariadb.connect(**BD)
    query = "SELECT login, ip_address, duration_seconds FROM Connections;" 
    df = pandas.read_sql(query, conn)



    # On crée un dataframe avec toute les valeurs de la requête
    df['duration_seconds'] = pandas.to_numeric(df['duration_seconds'], errors='coerce')
    idmax1 = df['duration_seconds'].idxmax()
    duration_heure = df.loc[idmax1,'duration_seconds']//3600
    duration_minutes = (df.loc[idmax1,'duration_seconds']%3600)//60

    # Récupération des données qui nous intéresse
    user_count = df['login'].value_counts()
    user_max = user_count.idxmax()
    user_connection_max = user_count.max()

    machine_count = df['ip_address'].value_counts()
    machine_max = machine_count.idxmax()
    machine_connection_max = machine_count.max()


    df['duration_minutes'] = df['duration_seconds']/60
    top5 = df.groupby('login')['duration_seconds'].mean().sort_values(ascending=False).head(5)



    # Création de la base de notre image avec un grid pour positionner les différents éléments
    tab = plt.figure(figsize=(12,8))
    grid = GridSpec(2,3,height_ratios=[1,2])
    
    #Fonction qui génère les élements similaire
    def generer(grid_place, titre, value1, value2, couleur):
        grid_place.axis('off')
        rectangle = plt.Rectangle((0,0), 1, 1, color=couleur, alpha=0.2, transform=grid_place.transAxes, linewidth=2,)
        grid_place.add_patch(rectangle)
        grid_place.text(0.5, 0.8, titre, ha='center', fontsize=10, color='black', fontweight='bold')
        grid_place.text(0.5, 0.55, value1, ha='center', fontsize=16, color='#000000', fontweight='bold')
        grid_place.text(0.5, 0.3, value2, ha='center', fontsize=14, color=couleur, fontweight='bold')


    # Placement des éléments
    grid_place = tab.add_subplot(grid[0, 0])
    generer(grid_place, "Plus longue session", df.loc[idmax1,'login'], f"{duration_heure}" + "h" + f"{duration_minutes}", "#e74c3c")
    
    grid_place = tab.add_subplot(grid[0, 1])
    generer(grid_place, "Utilisateur avec le plus de connexion", user_max, f"{user_connection_max} connexions", "#2980b9")
    
    grid_place = tab.add_subplot(grid[0, 2])
    generer(grid_place, "Machine la plus utilisée", machine_max, f"{machine_connection_max} connexions", "#27ae60")

    grid_place = tab.add_subplot(grid[1, :])
    top5.plot(kind='barh', ax=grid_place, color='#0a68cc', edgecolor='black')
    for login, temps in enumerate(top5):
        h = int(temps//3600)
        min = int(temps%3600//60)
        grid_place.text(temps, login, f" {h}h{min}", va='center', fontweight='bold')

    # Changement de l'affichage du graphe
    grid_place.invert_yaxis() # Le premier est tous en haut du tableau
    grid_place.set_title("Top 5 des durées moyennes de connexion", fontweight='bold', fontsize=14)
    grid_place.set_xticks([])
    grid_place.set_xlabel("")
    grid_place.set_ylabel("")   
    
    # Enregistrement de l'image
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