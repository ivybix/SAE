package Algo;

import java.io.FileNotFoundException;
import java.util.*;

public class CheminTest {
    private final Extraction extraction;
    private final List<String> sommets;
    private final List<Commande> commandes;

    public CheminTest() throws FileNotFoundException {
        extraction = new Extraction();
        sommets = new ArrayList<>();
        sommets.add("Velizy");
        sommets.addAll(extraction.getVilles().keySet());

        commandes = new ArrayList<>();
        for (Map.Entry<String, String> entry : extraction.getScenarios().get(0).entrySet()) {
            String vendeur = extraction.getMembresVilles().get(entry.getKey());
            String acheteur = extraction.getMembresVilles().get(entry.getValue());
            commandes.add(new Commande(vendeur, acheteur));
        }
    }

    public List<String> parcoursGlouton() {
        Set<String> villesAVisiter = new HashSet<>();
        Map<String, List<String>> livraisons = new HashMap<>();

        for (Commande c : commandes) {
            villesAVisiter.add(c.vendeur);
            villesAVisiter.add(c.acheteur);
            livraisons.computeIfAbsent(c.vendeur, k -> new ArrayList<>()).add(c.acheteur);
        }

        List<String> parcours = new ArrayList<>();
        Set<String> cartesRamassees = new HashSet<>();
        Set<Commande> commandesLivrees = new HashSet<>();

        String villeActuelle = "Velizy";
        parcours.add(villeActuelle);

        while (commandesLivrees.size() < commandes.size()) {
            String prochaineVille = null;
            int minDistance = Integer.MAX_VALUE;

            for (Commande c : commandes) {
                if (!cartesRamassees.contains(c.vendeur)) {
                    int d = extraction.distanceVilleToVille(villeActuelle, c.vendeur);
                    if (d < minDistance) {
                        minDistance = d;
                        prochaineVille = c.vendeur;
                    }
                } else if (cartesRamassees.contains(c.vendeur) && !commandesLivrees.contains(c)) {
                    int d = extraction.distanceVilleToVille(villeActuelle, c.acheteur);
                    if (d < minDistance) {
                        minDistance = d;
                        prochaineVille = c.acheteur;
                    }
                }
            }

            if (prochaineVille == null) break; // sécurité
            villeActuelle = prochaineVille;
            parcours.add(villeActuelle);

            for (Commande c : commandes) {
                if (villeActuelle.equals(c.vendeur)) {
                    cartesRamassees.add(c.vendeur);
                } else if (villeActuelle.equals(c.acheteur) && cartesRamassees.contains(c.vendeur)) {
                    commandesLivrees.add(c);
                }
            }
        }

        if (!villeActuelle.equals("Velizy")) parcours.add("Velizy");

        return parcours;
    }

    public List<Commande> getCommandes() {
        return commandes;
    }

    public List<String> getSommets() {
        return sommets;
    }

    public static class Commande {
        public final String vendeur;
        public final String acheteur;

        public Commande(String vendeur, String acheteur) {
            this.vendeur = vendeur;
            this.acheteur = acheteur;
        }

        public String toString() {
            return vendeur + " -> " + acheteur;
        }
    }
}