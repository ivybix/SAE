package Algo;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.HashMap;
import java.util.Objects;
import java.util.Scanner;

public class Extraction {
    private HashMap<String, String> echanges;
    private HashMap<Integer, HashMap<String, String>> scenarios;

    private HashMap<String, String> membresVilles;
    public Extraction() throws FileNotFoundException {
        File ressources = new File("src",File.separator+"Ressources");
        this.membresVilles = new HashMap<>();
        this.scenarios = new HashMap<Integer, HashMap<String, String>>();


        int Nscenario = 0;

        for (File f : Objects.requireNonNull(ressources.listFiles())) {
            if (Objects.equals(f.getName().split("_")[0], "scenario")) {

                Scanner lecteur = new Scanner(f);
                this.echanges = new HashMap<>();
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


}