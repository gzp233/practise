package com.cpdms.common.quartz.task;

import com.cpdms.mapper.examination.BizExaminationOrderMapper;
import com.cpdms.util.DateUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import java.util.Date;

/**
 *预约当天之后完成预约
 * @CLASSNAME   CancelExaminationOrder
 * @Description 定时调度具体工作类
 * @Auther V
 * @DATE 2019/11/22 11:33
 */
@Component("CancelExaminationOrder")
public class CancelExaminationOrder {
    @Autowired
    private BizExaminationOrderMapper bizExaminationOrderMapper;

    /**
     * 无参的任务
     */
    public void run() {
        Date date = DateUtils.getStartTime(new Date());
        bizExaminationOrderMapper.updateDelayOrder(DateUtils.format(date, DateUtils.DATE_PATTERN));
    }
}
