package taskrunner

import "time"

type Worker struct {
	ticker *time.Ticker
	runner *Runner
}

func NewWorker(internal time.Duration, r *Runner) *Worker {
	return &Worker{
		ticker: time.NewTicker(internal * time.Second),
		runner: r,
	}
}

func (w *Worker) StartWorker() {
	for {
		select {
		case <-w.ticker.C:
			go w.runner.StartAll()
		}
	}
}

func Start() {
	r := NewRunner(3, true, VideoClearDispatcher, VideoClearExecutor)
	w := NewWorker(3, r)
	go w.StartWorker()
}
