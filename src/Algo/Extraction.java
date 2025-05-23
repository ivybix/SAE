package Algo;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.*;

import static java.lang.Integer.parseInt;

public class Extraction {
    private final HashMap<Integer, HashMap<String, String>> scenarios;
    private final TreeMap<String, ArrayList<Integer>> distanceVilles;
    private final HashMap<String, String> membresVilles;
    public Extraction() throws FileNotFoundException {
        File ressources = new File("src",File.separator+"Ressources");
        this.membresVilles = new HashMap<>();
        this.scenarios = new HashMap<>();
        this.distanceVilles = new TreeMap<>();


        int Nscenario = 0;

        for (File f : Objects.requireNonNull(ressources.listFiles())) {

            if (f.getName().equals("distances.txt")) {
                Scanner lecteur = new Scanner(f);
                String line = "";

                while (lecteur.hasNextLine()) {
                    String data = lecteur.nextLine();
                    if (!data.split(" ")[0].equals("")) {
                        ArrayList<Integer> distances = new ArrayList<>();

                        for (String s : data.split(" ")) {
                            try {
                                int distance = Integer.parseInt(s);
                                distances.add(distance);

                            } catch (NumberFormatException e) {
                            }
                        }
                        distanceVilles.put(data.split(" ")[0], distances);
                    }
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


    public Map<String, List<String>> getVilles() {
        Map<String, List<String>> villes = new HashMap<>();
        for (Map.Entry<String, String> entry : this.getScenarios().get(0).entrySet()) {
            String vendeur = entry.getKey();
            String acheteur = entry.getValue();
            String ville = this.getMembresVilles().get(vendeur);
            String ville2 = this.getMembresVilles().get(acheteur);


            villes.putIfAbsent(ville, new ArrayList<>());

            villes.get(ville).add(ville2);
        }
        return villes;
    }
    public int distanceVilleToVille(String villeDepart, String villeArriver) {
        List<String> values = new ArrayList<>(distanceVilles.keySet());
        return distanceVilles.get(villeDepart).get(values.indexOf(villeArriver));
    }




}