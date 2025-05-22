package vue;

import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;

import java.io.FileNotFoundException;

public class HBoxRoot extends HBox {
    public HBoxRoot() throws FileNotFoundException {
        super();

        VBox vbox = new ScenarioPanel();
        this.getChildren().add(vbox);
    }
}
