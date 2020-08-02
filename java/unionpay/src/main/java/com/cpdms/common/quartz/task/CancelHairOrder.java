package com.cpdms.common.quartz.task;

import com.cpdms.mapper.hair.BizHairOrderMapper;
import com.cpdms.model.hair.BizHairOrder;
import com.cpdms.model.hair.BizHairOrderExample;
import com.cpdms.util.Constants;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import java.util.Date;

/**
 *预约时间之后30分钟不到店确认，取消美发预约
 * @CLASSNAME   CancelHairOrder
 * @Description 定时调度具体工作类
 * @Auther V
 * @DATE 2019/11/5 11:33
 */
@Component("CancelHairOrder")
public class CancelHairOrder {
    @Autowired
    private BizHairOrderMapper bizHairOrderMapper;

     /**
     * 无参的任务
     */
    public void run() {
        BizHairOrderExample bizHairOrderExample = new BizHairOrderExample();
        bizHairOrderExample.createCriteria().andStatusEqualTo(1).andOrdTimeLessThan(new Date(System.currentTimeMillis() - 30 * 60 * 1000)).andOrdStateEqualTo(Constants.HAIR_ORD_ORDER);
        BizHairOrder bizHairOrder = new BizHairOrder();
        bizHairOrder.setOrdState(Constants.HAIR_ORD_CANCEL);
        bizHairOrder.setUpdateDate(new Date());
        bizHairOrder.setOrdMemo("预约时间30分钟内未确认，系统自动取消");
        bizHairOrderMapper.updateByExampleSelective(bizHairOrder, bizHairOrderExample);
    }
}
