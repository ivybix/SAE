package Algo;

class Commande {
    public final String vendeur;
    public final String acheteur;

    public Commande(String vendeur, String acheteur) {
        this.vendeur = vendeur;
        this.acheteur = acheteur;
    }

    @Override
    public String toString() {
        return vendeur + " -> " + acheteur;
    }
}