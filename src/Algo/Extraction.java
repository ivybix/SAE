package Algo;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Objects;
import java.util.Scanner;

public class Extraction {
    private HashMap<String, String> scenarios;
    private HashMap<String, String> membresVilles;
    public Extraction() throws FileNotFoundException {
        File ressources = new File("src",File.separator+"Ressources");
        this.membresVilles = new HashMap<>();
        this.scenarios = new HashMap<>();


        for (File f : Objects.requireNonNull(ressources.listFiles())) {
            if (Objects.equals(f.getName().split("_")[0], "scenario")) {
                Scanner lecteur = new Scanner(f);
                while (lecteur.hasNextLine()) {
                    String data = lecteur.nextLine();
                    scenarios.put(data.split(" -> ")[0], data.split(" -> ")[1]);

                }
                lecteur.close();
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

    public HashMap<String, String> getScenarios() {
        return scenarios;
    }
    public HashMap<String, String> getMembresVilles() {
        return membresVilles;
    }


}