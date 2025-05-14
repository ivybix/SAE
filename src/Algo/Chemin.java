package Algo;
import java.io.FileNotFoundException;
import java.util.*;
public class Chemin {
    private final List <String> sommets;
    private Map <String, List<String>> voisins;
    public Chemin() throws FileNotFoundException {
        this.sommets = new ArrayList <> ();
        this.voisins = new HashMap <> ();
        sommets.add ("Velizy");
        for ( Map.Entry<String, List<String>> entry : new Extraction().getVilles().entrySet()) {
            sommets.add (entry.getKey());
        }

        for ( String ville : sommets) {
            if ( ville.equals("Velizy")) {
                voisins.put (ville, sommets.subList(sommets.indexOf(ville) + 1, sommets.size()));
            } else {
                List<String> listeVoisins =  new Extraction().getVilles().get(ville);
                listeVoisins.add("Velizy");
                voisins.put (ville, listeVoisins);
            }
        }
        System.out.println (voisins);

    }
    public List <String> getSommets() {
        return sommets;
    }
}
