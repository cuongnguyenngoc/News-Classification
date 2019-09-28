<?php

namespace NewsClassification\Classification;

use Phpml\Classification\NaiveBayes;
use Phpml\ModelManager;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\NGramTokenizer;
use Phpml\FeatureExtraction\StopWords\English;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Classification\SVC;
use Phpml\Pipeline;
use Phpml\Exception\FileException;
use Phpml\Classification\KNearestNeighbors;

use NewsClassification\Utils\DataProcessor;


class ClassifyModel {

    private $model;
    private $dataProcessor;
    private $modelType;

    public function __construct($modelType) {
        $this->dataProcessor = new DataProcessor;
        $this->modelType = $modelType;
    }

    public function getModel() {
        $this->model = new SVC();
    }

    public function usingModelUsingPipeline() {
        switch ($this->modelType) {
            case "naviebayes":
                $this->model = new Pipeline([
                    new TokenCountVectorizer(new NGramTokenizer(1, 3), new English()),
                    new TfIdfTransformer()
                ], new NaiveBayes());
                break;
            case "knn":
                $this->model = new Pipeline([
                    new TokenCountVectorizer(new NGramTokenizer(1, 3), new English()),
                    new TfIdfTransformer()
                ], new KNearestNeighbors());
                break;
            case "svc":
                $this->model = new Pipeline([
                    new TokenCountVectorizer(new NGramTokenizer(1, 3), new English()),
                    new TfIdfTransformer()
                ], new SVC());
                break;
        }
        return $this->model;
    }

    public function getModelFromSavedModel($filepath) {
        
        $isExist = $this->dataProcessor->checkFilExists($filepath);

        if ($isExist) {
            $modelManager = new ModelManager();
            $this->model = $modelManager->restoreFromFile($filepath);
            
            return $this->model;
        }
        throw FileException::missingFile(basename($filepath));
        return null;
    }

    public function train($samples, $labels)
    {
        $this->model->train($samples, $labels);
    }

    public function predict($samples) {
        return $this->model->predict($samples);
    }

    public function saveModel($filepath) {
        $modelManager = new ModelManager();
        $modelManager->saveToFile($this->model, $filepath);
    }
}