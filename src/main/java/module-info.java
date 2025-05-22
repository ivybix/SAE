module sae.saeihm {
    requires javafx.controls;
    requires javafx.fxml;

    requires org.controlsfx.controls;

    opens sae.saeihm to javafx.fxml;
    exports sae.saeihm;

    exports modele;
    exports vue;
}