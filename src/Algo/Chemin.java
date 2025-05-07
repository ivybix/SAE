package Algo;
import java.util.*;
public class Chemin {
    private final List <String> sommets;
    private Map <Integer, Set<Integer>> voisins;
    public Chemin() {
        this.sommets = new ArrayList <> ();
        this.voisins = new HashMap <> ();

    }

}
