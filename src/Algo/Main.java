package Algo;

import java.io.FileNotFoundException;
import java.util.List;
import java.util.Map;


public class Main {
    public static void main(String[] args) throws FileNotFoundException {
        Extraction e = new Extraction();
        Chemin ch = new Chemin();
        System.out.println(e.getVilles());
        System.out.println(e.getScenarios());
        System.out.println(e.getMembresVilles());
        System.out.println(ch.getSommets());

        CheminTest chemin = new CheminTest();
        List<String> parcours = chemin.parcoursGlouton();

        System.out.println("--- Parcours glouton ---");
        for (String ville : parcours) {
            System.out.print(ville + " -> ");
        }
        System.out.println("FIN");

        System.out.println("FIN");
// CALCUL DES DISTANCES ENTRE TOUTES LES VILLES
//        System.out.println("\n--- Distances ---");
//        for (String from : chemin.getDistances().keySet()) {
//            for (Map.Entry<String, Integer> entry : chemin.getDistances().get(from).entrySet()) {
//                System.out.println(from + " -> " + entry.getKey() + " : " + entry.getValue() + " km");
//            }
//        }
    }
}

//        Algo1 a = new Algo1();
//        System.out.println(a);