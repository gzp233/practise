package worker

import (
	"encoding/json"
	"io/ioutil"
)

// 程序配置
type Config struct {
	EtcdEndpoints         []string `json:"etcdEndpoints"`
	EtcdDialTimeout       int      `json:"etcdDialTimeout"`
	MongodbUri            string   `json:"mongodbUri"`
	MongodbConnectTimeout int      `json:"mongodbConnectTimeout"`
	JobLogBatchSize       int      `json:"jobLogBatchSize"`
	JobLogCommitTimeout   int      `json"jobLogCommitTimeout"`
}

var (
	// 单例
	G_config *Config
)

// 加载配置
func InitConfig(filename string) (err error) {
	var (
		content []byte
		conf    Config
	)
	if content, err = ioutil.ReadFile(filename); err != nil {
		return
	}
	// 读取配置
	if err = json.Unmarshal(content, &conf); err != nil {
		return
	}
	// 赋值单例
	G_config = &conf

	return
}
