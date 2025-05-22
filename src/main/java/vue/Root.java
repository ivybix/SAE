package vue;
import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

public class Root extends Application {


    @Override
    public void start(Stage stage) throws Exception {
        VBox root = new VBox();
        root.getChildren().add(new Label("Hello World!"));
        Scene scene = new Scene(root );
        root.setSpacing(10);
        stage.setScene(scene);
        stage.show();
    }
    public static void main(String[] args) {
        Application.launch();
    }
}
