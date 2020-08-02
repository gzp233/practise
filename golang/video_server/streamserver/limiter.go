package main

import "log"

type ConnLimiter struct {
	concurentConn int
	bucket        chan int
}

func NewConnLimiter(cc int) *ConnLimiter {
	return &ConnLimiter{
		concurentConn: cc,
		bucket:        make(chan int, cc),
	}
}

func (cl *ConnLimiter) GetConn() bool {
	if len(cl.bucket) >= cl.concurentConn {
		log.Printf("Reached the rate limitation.")
		return false
	}

	cl.bucket <- 1
	return true
}

func (cl *ConnLimiter) ReleaseCon() {
	c := <-cl.bucket
	log.Printf("New connection comming:%d", c)
}
