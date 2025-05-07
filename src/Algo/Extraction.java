package Algo;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.*;

import static java.lang.Integer.parseInt;

public class Extraction {
    private final HashMap<Integer, HashMap<String, String>> scenarios;

    private final HashMap<String, String> membresVilles;
    public Extraction() throws FileNotFoundException {
        File ressources = new File("src",File.separator+"Ressources");
        this.membresVilles = new HashMap<>();
        this.scenarios = new HashMap<>();


        int Nscenario = 0;

        for (File f : Objects.requireNonNull(ressources.listFiles())) {

            if (f.getName().equals("distances.txt")) {
                Scanner lecteur = new Scanner(f);
                String line;
                HashMap<String, HashSet<Integer>> cities = new HashMap<>();

                while (lecteur.hasNextLine()) {
                    String data = lecteur.nextLine();
                    if (!data.split(" ")[0].equals("")) {
                        HashSet<Integer> distances = new HashSet<>();

                        for (String s : data.split(" ")) {
                            try {
                                int distance = Integer.parseInt(s);
                                distances.add(distance);

                            } catch (NumberFormatException e) {
                                System.out.println("Valeur ignorée (non numérique) : " + s);
                            }
                        }
                        cities.put(data.split(" ")[0], distances);
                    }
                    System.out.println(cities);

                }
                lecteur.close();
            }

            if (Objects.equals(f.getName().split("_")[0], "scenario")) {

                Scanner lecteur = new Scanner(f);
                HashMap<String, String> echanges = new HashMap<>();
                while (lecteur.hasNextLine()) {
                    String data = lecteur.nextLine();
                    echanges.put(data.split(" -> ")[0], data.split(" -> ")[1]);

                }
                scenarios.put(Nscenario, echanges);
                lecteur.close();
                Nscenario++;
            }
            if (Objects.equals(f.getName().split("_")[0], "membres")) {
                Scanner lecteur = new Scanner(f);
                while (lecteur.hasNextLine()) {
                    String data = lecteur.nextLine();
                    membresVilles.put(data.split(" ")[0], data.split(" ")[1]);
                }
                lecteur.close();


            }

        }

    }

    public HashMap<Integer, HashMap<String, String>> getScenarios() {
        return scenarios;
    }
    public HashMap<String, String> getMembresVilles() {
        return membresVilles;
    }
    public HashSet<String> getVille() {
        HashSet<String> villes = new HashSet<>();
        for (Map.Entry<String, String> entry : this.getScenarios().get(0).entrySet()) {
            String vendeur = entry.getKey();
            String acheteur = entry.getValue();
            String ville = this.getMembresVilles().get(vendeur);
            String ville2 = this.getMembresVilles().get(acheteur);
            villes.add(ville + "->" + ville2);

        }
        return villes;
    }



}