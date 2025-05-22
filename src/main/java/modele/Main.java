package modele;

import java.io.FileNotFoundException;
import java.util.List;
import java.util.Map;


public class Main {
    public static void main(String[] args) throws FileNotFoundException {
//        Extraction e = new Extraction();
//        Chemin ch = new Chemin();
//        System.out.println(e.getVilles());
//        System.out.println(e.getScenarios());
//        System.out.println(e.getMembresVilles());
//        System.out.println(ch.getSommets());

        Extraction extraction = new Extraction();

        System.out.println("Nombre total de scénarios détectés : " + extraction.getScenarios().size());

        for (int i = 0; i < extraction.getScenarios().size(); i++) {
            System.out.println("=== SCÉNARIO " + i + " ===");
            try {
                CheminTest chemin = new CheminTest(4);
                List<CheminTest.Commande> commandes = chemin.getCommandes();

                if (commandes.isEmpty()) {
                    System.out.println("Aucune commande trouvée dans ce scénario. Fichier vide ou mal formaté ?\n");
                    continue;
                }

                System.out.println("Commandes :");
                for (CheminTest.Commande c : commandes) {
                    System.out.println(" - " + c);
                }
                System.out.println(chemin.parcoursGlouton());

                List<String> parcours = chemin.parcoursGlouton();
                System.out.print("Parcours : ");
                for (String ville : parcours) {
                    System.out.print(ville + " -> ");
                }
                System.out.println("FIN");

                int distance = chemin.calculDistance(parcours);
                System.out.println("Distance totale : " + distance + " km\n");

            } catch (Exception e) {
                System.out.println("⚠️ Erreur dans le scénario " + i + " : " + e.getMessage());
                e.printStackTrace();
                System.out.println();
            }
        }
    }}

//        Algo1 a = new Algo1();
//        System.out.println(a);