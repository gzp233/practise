package com.cpdms.service.examination;

import java.util.Date;
import java.util.HashMap;
import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BaseEmpMapper;
import com.cpdms.mapper.examination.BaseExaminationMapper;
import com.cpdms.mapper.examination.BizExaminationOrdItemMapper;
import com.cpdms.mapper.examination.BizExaminationOrderMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.dinning.BaseEmp;
import com.cpdms.model.examination.*;
import com.cpdms.util.Constants;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 * 体检预约表 BizExaminationOrderService
 *
 * @author Eric_自动生成
 * @Title: BizExaminationOrderService.java 
 * @Package com.cpdms.hair.service 
 * @email eric@gmail.com
 * @date 2019-11-18 11:53:05  
 **/
@Service
public class BizExaminationOrderService implements BaseService<BizExaminationOrder, BizExaminationOrderExample> {
    @Autowired
    private BizExaminationOrderMapper bizExaminationOrderMapper;
    @Autowired
    private BaseEmpMapper baseEmpMapper;
    @Autowired
    private BaseExaminationMapper baseExaminationMapper;
    @Autowired
    private BizExaminationOrdItemMapper bizExaminationOrdItemMapper;


    /**
     * 分页查询
     *
     * @param pageNum
     * @param pageSize
     * @return
     */
    public PageInfo<BizExaminationOrder> list(Tablepar tablepar, BizExaminationOrderExample example) {
        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
        List<BizExaminationOrder> list = bizExaminationOrderMapper.selectByExample(example);
        PageInfo<BizExaminationOrder> pageInfo = new PageInfo<BizExaminationOrder>(list);
        return pageInfo;
    }

    // 保存订单
    public HashMap<String, String> saveOrder(BizExaminationOrder bizExaminationOrder) {
        HashMap<String, String> result = new HashMap<>();
        result.put("code", "1");
        BaseEmp baseEmp = baseEmpMapper.selectByCardNo(bizExaminationOrder.getCardNo());
        if (baseEmp == null) {
            result.put("msg", "该卡号不存在");
            return result;
        }
        // 当天不能多次预约
        BizExaminationOrderExample be = new BizExaminationOrderExample();
        be.createCriteria().andCardNoEqualTo(bizExaminationOrder.getCardNo()).andOrdStateNotEqualTo(Constants.EXAMINATION_ORD_CANCEL).andOrdTimeEqualTo(bizExaminationOrder.getOrdTime());
        List<BizExaminationOrder> bs = bizExaminationOrderMapper.selectByExample(be);
        if (bs.size() > 0) {
            result.put("msg", "无法重复预约");
            return result;
        }

        bizExaminationOrder.setOrdCode(SnowflakeIdWorker.getUUID());
        bizExaminationOrder.setId(SnowflakeIdWorker.getUUID());
        bizExaminationOrder.setOrdEmpId(baseEmp.getId());
        bizExaminationOrder.setCreateBy(baseEmp.getEmpName());
        bizExaminationOrder.setCreateDate(new Date());
        bizExaminationOrder.setUpdateBy(baseEmp.getEmpName());
        bizExaminationOrder.setUpdateDate(new Date());
        bizExaminationOrder.setStatus(1);
        bizExaminationOrder.setOrdState(Constants.EXAMINATION_ORD_ORDER);
        List<BizExaminationOrdItem> bizExaminationOrdItemList = bizExaminationOrder.getBizExaminationOrdItemList();
        if (bizExaminationOrdItemList.size() == 0) {
            result.put("msg", "预约套餐不能为空");
            return result;
        }
        for (BizExaminationOrdItem bizExaminationOrdItem : bizExaminationOrdItemList) {
            BaseExamination baseExamination = baseExaminationMapper.selectByPrimaryKey(bizExaminationOrdItem.getExaminationId());
            if (baseExamination == null) {
                result.put("msg", "该套餐不存在");
                return result;
            }
            // 判断数量是否超过
            if (baseExamination.getOrderNum() >= baseExamination.getNum()) {
                result.put("msg", "预约数量超过限制");
                return result;
            }
            bizExaminationOrdItem.setId(SnowflakeIdWorker.getUUID());
            bizExaminationOrdItem.setExaminationOrdId(bizExaminationOrder.getId());
            bizExaminationOrdItem.setExaminationName(baseExamination.getExaminationName());
            bizExaminationOrdItem.setStartTime(baseExamination.getStartTime());
            bizExaminationOrdItem.setEndTime(baseExamination.getEndTime());
            baseExamination.setOrderNum(baseExamination.getOrderNum() + 1);
            bizExaminationOrdItem.setBaseExamination(baseExamination);
        }
        bizExaminationOrderMapper.insertSelective(bizExaminationOrder);
        for (BizExaminationOrdItem bizExaminationOrdItem : bizExaminationOrdItemList) {
            bizExaminationOrdItemMapper.insertSelective(bizExaminationOrdItem);
            baseExaminationMapper.updateByPrimaryKeySelective(bizExaminationOrdItem.getBaseExamination());
        }
        result.put("msg", bizExaminationOrder.getId());
        result.put("code", "0");
        return result;
    }

    @Override
    public int deleteByPrimaryKey(String ids) {

        List<String> lista = Convert.toListStrArray(ids);
        BizExaminationOrderExample example = new BizExaminationOrderExample();
        example.createCriteria().andIdIn(lista);
        return bizExaminationOrderMapper.deleteByExample(example);


    }


    @Override
    public BizExaminationOrder selectByPrimaryKey(String id) {

        return bizExaminationOrderMapper.selectByPrimaryKey(id);

    }


    @Override
    public int updateByPrimaryKeySelective(BizExaminationOrder record) {
        if (record.getOrdState() != null && record.getOrdState().equals(Constants.EXAMINATION_ORD_CANCEL)) {
            BizExaminationOrder bizExaminationOrder = bizExaminationOrderMapper.selectByPrimaryKey(record.getId());
            if (bizExaminationOrder.getBizExaminationOrdItemList().size() > 0) {
                for (BizExaminationOrdItem bizExaminationOrdItem : bizExaminationOrder.getBizExaminationOrdItemList()) {
                    BaseExamination baseExamination= baseExaminationMapper.selectByPrimaryKey(bizExaminationOrdItem.getExaminationId());
                    baseExamination.setOrderNum(baseExamination.getOrderNum() - 1);
                    baseExamination.setUpdateDate(new Date());
                    baseExaminationMapper.updateByPrimaryKeySelective(baseExamination);
                }
            }
        }
        return bizExaminationOrderMapper.updateByPrimaryKeySelective(record);
    }


    /**
     * 添加
     */
    @Override
    public int insertSelective(BizExaminationOrder record) {
        //添加雪花主键id
        record.setId(SnowflakeIdWorker.getUUID());


        return bizExaminationOrderMapper.insertSelective(record);
    }


    @Override
    public int updateByExampleSelective(BizExaminationOrder record, BizExaminationOrderExample example) {

        return bizExaminationOrderMapper.updateByExampleSelective(record, example);
    }


    @Override
    public int updateByExample(BizExaminationOrder record, BizExaminationOrderExample example) {

        return bizExaminationOrderMapper.updateByExample(record, example);
    }

    @Override
    public List<BizExaminationOrder> selectByExample(BizExaminationOrderExample example) {

        return bizExaminationOrderMapper.selectByExample(example);
    }


    @Override
    public long countByExample(BizExaminationOrderExample example) {

        return bizExaminationOrderMapper.countByExample(example);
    }


    @Override
    public int deleteByExample(BizExaminationOrderExample example) {

        return bizExaminationOrderMapper.deleteByExample(example);
    }

    /**
     * 检查name
     *
     * @param bizExaminationOrder
     * @return
     */
    public int checkNameUnique(BizExaminationOrder bizExaminationOrder) {
        BizExaminationOrderExample example = new BizExaminationOrderExample();
        example.createCriteria().andOrdCodeEqualTo(bizExaminationOrder.getOrdCode());
        List<BizExaminationOrder> list = bizExaminationOrderMapper.selectByExample(example);
        return list.size();
    }


}
