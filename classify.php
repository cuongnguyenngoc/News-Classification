<?php

require __DIR__ . '/vendor/autoload.php';

use NewsClassification\NewsClassify;
use NewsClassification\Utils\DataProcessor;

if(isset($_POST['news'])) {

    $news = $_POST['news'];

    file_put_contents('php://stderr', print_r($news, TRUE));
    $newsClassify = new NewsClassify("src/datasets/raw/bbc");

    $dataProcessor = new DataProcessor;
    $isExist = $dataProcessor->checkFilExists("newsclassification.svc.model");

    if (!$isExist) {
        $data['message'] = "Model is not exist so the system will train the classifier";
        $newsClassify->trainingAndSaveModel("svc");
    }

    // echo "Accuracy: ".$newsClassify->getClassifyPerformance("svc")."<br>";
    $data["category"] =  $newsClassify->classifyText($news, "svc");
    echo json_encode($data);
}

