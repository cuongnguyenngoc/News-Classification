<?php

namespace NewsClassification;

set_time_limit(0);
ini_set('memory_limit', -1);

use NewsClassification\Utils\DataProcessor;
use NewsClassification\Classification\ClassifyModel;
use Phpml\Metric\Accuracy;

class NewsClassify {

    private $dataProcessor;

    public function __construct($datapath) {
        $this->dataProcessor = new DataProcessor();
        $dataset = $this->dataProcessor->getDataset($datapath);
        $this->dataProcessor->seperateTrainingAndTesting($dataset);
    }

    public function trainingAndSaveModel($modelType) {
        $classifyModel = new ClassifyModel($modelType);

        $classifyModel->usingModelUsingPipeline();

        $classifyModel->train($this->dataProcessor->getTrainingSamples(), $this->dataProcessor->getTrainingLabels());

        $classifyModel->saveModel("newsclassification.".$modelType.".model");
    }

    public function getClassifyPerformance($modelType) {
        $classifyModel = new ClassifyModel($modelType);
        $newModel = $classifyModel->getModelFromSavedModel("newsclassification.".$modelType.".model");
        $predictedLabels = $newModel->predict($this->dataProcessor->getTestSamples());

        return Accuracy::score($this->dataProcessor->getTestLabels(), $predictedLabels);
    }

    public function classifyText($text, $modelType) {
        
        $text = $this->clean($text);
        $classifyModel = new ClassifyModel($modelType);
        $model = $classifyModel->getModelFromSavedModel("newsclassification.".$modelType.".model");
        return $model->predict([$text])[0];
    }

    private function clean($string) {
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
    }
}