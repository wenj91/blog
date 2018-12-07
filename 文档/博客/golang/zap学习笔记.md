

## 定制日志打印

```golang
// loglevel 日志级别
func initLogger(logpath string, loglevel string) *zap.Logger {
	hook := lumberjack.Logger{
		Filename:   logpath, // 日志文件路径
		MaxSize:    1024,    // megabytes
		MaxBackups: 3,       // 最多保留3个备份
		MaxAge:     7,       //days
		Compress:   true,    // 是否压缩 disabled by default
	}

	var level zapcore.Level
	switch loglevel {
	case "debug":
		level = zap.DebugLevel
	case "info":
		level = zap.InfoLevel
	case "error":
		level = zap.ErrorLevel
	default:
		level = zap.InfoLevel
	}

	topicErrors := zapcore.AddSync(ioutil.Discard)
	fileWriter := zapcore.AddSync(&hook)

	// High-priority output should also go to standard error, and low-priority
	// output should also go to standard out.
	consoleDebugging := zapcore.Lock(os.Stdout)

	// Optimize the Kafka output for machine consumption and the console output
	// for human operators.
	kafkaEncoder := zapcore.NewJSONEncoder(zap.NewProductionEncoderConfig())
	consoleEncoder := zapcore.NewConsoleEncoder(zap.NewDevelopmentEncoderConfig())

	// Join the outputs, encoders, and level-handling functions into
	// zapcore.Cores, then tee the four cores together.
	core := zapcore.NewTee(
		// 打印在kafka topic中（伪造的case）
		zapcore.NewCore(kafkaEncoder, topicErrors, level),
		// 打印在控制台
		zapcore.NewCore(consoleEncoder, consoleDebugging, level),
		// 打印在文件中
		zapcore.NewCore(consoleEncoder, fileWriter, level),
	)

    logger := zap.New(core, 
    zap.AddCaller(),  // 添加文件调用打印
    zap.AddCallerSkip(1)) // 跳过1打印真正日志文件

	logger.Info("DefaultLogger init success") // log 初始化成功

	return logger
}

```