package com.cpdms.service.hair;

import java.math.BigDecimal;
import java.util.Date;
import java.util.HashMap;
import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BaseEmpMapper;
import com.cpdms.mapper.hair.BaseHairMapper;
import com.cpdms.mapper.hair.BaseHairSettingMapper;
import com.cpdms.mapper.hair.BizHairOrdHairMapper;
import com.cpdms.mapper.hair.BizHairOrderMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.dinning.BaseEmp;
import com.cpdms.model.hair.*;
import com.cpdms.util.Constants;
import com.cpdms.util.DateUtils;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 * 美发预定订单表 BizHairOrderService
 *
 * @author Eric_自动生成
 * @Title: BizHairOrderService.java 
 * @Package com.cpdms.hair.service 
 * @email eric@gmail.com
 * @date 2019-11-14 10:55:57  
 **/
@Service
public class BizHairOrderService implements BaseService<BizHairOrder, BizHairOrderExample> {
    @Autowired
    private BizHairOrderMapper bizHairOrderMapper;
    @Autowired
    private BaseEmpMapper baseEmpMapper;
    @Autowired
    private BizHairOrdHairMapper bizHairOrdHairMapper;
    @Autowired
    private BaseHairMapper baseHairMapper;
    @Autowired
    private BaseHairSettingMapper baseHairSettingMapper;

    /**
     * 分页查询
     *
     * @param pageNum
     * @param pageSize
     * @return
     */
    public PageInfo<BizHairOrder> list(Tablepar tablepar, BizHairOrder bizHairOrder) {
        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
        List<BizHairOrder> list = bizHairOrderMapper.getOrderList(bizHairOrder);
        PageInfo<BizHairOrder> pageInfo = new PageInfo<BizHairOrder>(list);
        return pageInfo;
    }

    // 保存订单
    public HashMap<String, String> saveOrder(BizHairOrder bizHairOrder) {
        HashMap<String, String> result = new HashMap<>();
        result.put("code", "1");
        BaseEmp baseEmp = baseEmpMapper.selectByCardNo(bizHairOrder.getCardNo());
        if (baseEmp == null) {
            result.put("msg", "该卡号不存在");
            return result;
        }
        // 判断有没有重复预约
        BizHairOrderExample be = new BizHairOrderExample();
        be.createCriteria().andCardNoEqualTo(bizHairOrder.getCardNo()).andOrdStateNotEqualTo(Constants.HAIR_ORD_CANCEL)
                .andOrdTimeBetween(DateUtils.getStartTime(bizHairOrder.getOrdTime()), DateUtils.getEndTime(bizHairOrder.getOrdTime()));
        List<BizHairOrder> bs = bizHairOrderMapper.selectByExample(be);
        if (bs.size() > 0) {
            result.put("msg", "无法重复预约");
            return result;
        }
        // 判断有没有满
        Integer limit = getSettingNum(bizHairOrder.getOrdTime());
        HashMap<String, Integer> hashMap = getOrderStatusByTime(bizHairOrder.getOrdTime());
        String key = DateUtils.format(bizHairOrder.getOrdTime(), DateUtils.DATE_TIME_PATTERN) + " - " + DateUtils.format(bizHairOrder.getOrdEndTime(), DateUtils.DATE_TIME_PATTERN);
        if (hashMap.get(key) >= limit) {
            result.put("msg", "预约已满");
            return result;
        }
        bizHairOrder.setOrdEmpId(baseEmp.getId());
        bizHairOrder.setOrdCode(SnowflakeIdWorker.getUUID());
        bizHairOrder.setId(SnowflakeIdWorker.getUUID());
        bizHairOrder.setCreateBy(baseEmp.getEmpName());
        bizHairOrder.setCreateDate(new Date());
        bizHairOrder.setUpdateBy(baseEmp.getEmpName());
        bizHairOrder.setUpdateDate(new Date());
        bizHairOrder.setStatus(1);
        bizHairOrder.setOrdState(Constants.HAIR_ORD_ORDER);
        List<BizHairOrdHair> bizHairOrdHairList = bizHairOrder.getBizHairOrdHairList();
        if (bizHairOrdHairList.size() == 0) {
            result.put("msg", "预约项目不能为空");
            return result;
        }
        BigDecimal ordAmt = new BigDecimal(0);
        for (BizHairOrdHair bizHairOrdHair : bizHairOrdHairList) {
            BaseHair hailOld = baseHairMapper.selectByPrimaryKey(bizHairOrdHair.getHairId());
            if (hailOld == null) {
                result.put("msg", "预约项目不存在");
                return result;
            }
            bizHairOrdHair.setId(SnowflakeIdWorker.getUUID());
            bizHairOrdHair.setHairOrdId(bizHairOrder.getId());
            ordAmt = ordAmt.add(bizHairOrdHair.getPrice());
        }
        bizHairOrder.setOrdAmt(ordAmt);
        bizHairOrderMapper.insertSelective(bizHairOrder);
        for (BizHairOrdHair bizHairOrdHair : bizHairOrdHairList) {
            bizHairOrdHairMapper.insertSelective(bizHairOrdHair);
        }

        result.put("code", "0");
        result.put("msg", bizHairOrder.getId());
        return result;
    }

    public HashMap<String, Integer> getOrderStatusByTime(Date date) {

        // 统计每个时段的订单人数
        BizHairOrderExample example = new BizHairOrderExample();
        example.createCriteria().andOrdStateNotEqualTo(Constants.HAIR_ORD_CANCEL).andOrdTimeBetween(DateUtils.getStartTime(date), DateUtils.getEndTime(date));
        List<BizHairOrder> bizHairOrderList = bizHairOrderMapper.selectByExample(example);
        Integer minute = getSettingTime(date);
        HashMap<String, Integer> map = getSplitMap(date, minute);
        if (bizHairOrderList.size() > 0) {
            for (BizHairOrder bizHairOrder:bizHairOrderList) {
                String key = DateUtils.format(bizHairOrder.getOrdTime(), DateUtils.DATE_TIME_PATTERN) + " - " + DateUtils.format(bizHairOrder.getOrdEndTime(), DateUtils.DATE_TIME_PATTERN);
                map.put(key, map.get(key) + 1);
            }
        }
        return map;
    }

    public Boolean canOrder(Date date) {
        BizHairOrderExample example = new BizHairOrderExample();
        example.createCriteria().andStatusEqualTo(1).andOrdTimeBetween(DateUtils.getStartTime(date), DateUtils.getEndTime(date));
        long count = bizHairOrderMapper.countByExample(example);
        Integer minute = getSettingTime(date);

        return (count < 540/minute);

    }

    // 根据日期获取当天的时间段
    public HashMap<String, Integer> getSplitMap(Date date, Integer minute) {
        HashMap<String, Integer> map = new HashMap<>();
        // 先加到8点半
        Date start = DateUtils.addMinutes(DateUtils.getStartTime(date), 510);
        String key = "";
        for (Integer i = 0; i < 540 / minute; i++) {
            key = DateUtils.format(start, DateUtils.DATE_TIME_PATTERN);
            key += " - ";
            start = DateUtils.addMinutes(start, minute);
            key += DateUtils.format(start, DateUtils.DATE_TIME_PATTERN);
            map.put(key, 0);
        }

        return map;
    }

    // 获取当天每个时间段的限制人数
    public Integer getSettingNum(Date date) {
        BaseHairSetting stepNumSetting = baseHairSettingMapper.selectSetting("NUM_PER_STEP", DateUtils.format(date, DateUtils.DATE_PATTERN));
        return Integer.valueOf(stepNumSetting.getSettingValue());
    }

    // 获取当天每个时间段的长度
    public Integer getSettingTime(Date date) {
        BaseHairSetting stepNumSetting = baseHairSettingMapper.selectSetting("STEP_TIME", DateUtils.format(date, DateUtils.DATE_PATTERN));
        return Integer.valueOf(stepNumSetting.getSettingValue());
    }

    @Override
    public int deleteByPrimaryKey(String ids) {

        List<String> lista = Convert.toListStrArray(ids);
        BizHairOrderExample example = new BizHairOrderExample();
        example.createCriteria().andIdIn(lista);
        return bizHairOrderMapper.deleteByExample(example);


    }


    @Override
    public BizHairOrder selectByPrimaryKey(String id) {

        return bizHairOrderMapper.selectByPrimaryKey(id);

    }

    @Override
    public int updateByPrimaryKeySelective(BizHairOrder record) {
        return bizHairOrderMapper.updateByPrimaryKeySelective(record);
    }


    /**
     * 添加
     */
    @Override
    public int insertSelective(BizHairOrder record) {
        //添加雪花主键id
        record.setId(SnowflakeIdWorker.getUUID());


        return bizHairOrderMapper.insertSelective(record);
    }


    @Override
    public int updateByExampleSelective(BizHairOrder record, BizHairOrderExample example) {

        return bizHairOrderMapper.updateByExampleSelective(record, example);
    }


    @Override
    public int updateByExample(BizHairOrder record, BizHairOrderExample example) {

        return bizHairOrderMapper.updateByExample(record, example);
    }

    @Override
    public List<BizHairOrder> selectByExample(BizHairOrderExample example) {

        return bizHairOrderMapper.selectByExample(example);
    }


    @Override
    public long countByExample(BizHairOrderExample example) {

        return bizHairOrderMapper.countByExample(example);
    }


    @Override
    public int deleteByExample(BizHairOrderExample example) {

        return bizHairOrderMapper.deleteByExample(example);
    }

    /**
     * 检查name
     *
     * @param bizHairOrder
     * @return
     */
    public int checkNameUnique(BizHairOrder bizHairOrder) {
        BizHairOrderExample example = new BizHairOrderExample();
        example.createCriteria().andOrdCodeEqualTo(bizHairOrder.getOrdCode());
        List<BizHairOrder> list = bizHairOrderMapper.selectByExample(example);
        return list.size();
    }


}
