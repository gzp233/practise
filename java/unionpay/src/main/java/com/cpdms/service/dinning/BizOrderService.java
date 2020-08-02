package com.cpdms.service.dinning;

import java.math.BigDecimal;
import java.util.Date;
import java.util.List;
import java.util.Map;
import java.util.Arrays;

import com.cpdms.mapper.dinning.BaseEmpMapper;
import com.cpdms.mapper.dinning.BaseFoodMapper;
import com.cpdms.mapper.dinning.BizOrdFoodMapper;
import com.cpdms.model.dinning.*;
import com.cpdms.util.Constants;
import com.cpdms.util.DateUtils;
import com.cpdms.util.StringUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BizOrderMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.transaction.annotation.Transactional;

/**
 * 预定订单 BizOrderService
 * @Title: BizOrderService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:42:45  
 **/
@Service
@Transactional
public class BizOrderService implements BaseService<BizOrder, BizOrderExample>{
	@Autowired
	private BizOrderMapper bizOrderMapper;

	@Autowired
    private BizOrdFoodMapper bizOrdFoodMapper;

	@Autowired
    private BaseEmpMapper baseEmpMapper;

	@Autowired
    private BaseFoodMapper baseFoodMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BizOrder> list(Tablepar tablepar,String name){
	        BizOrderExample testExample=new BizOrderExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andOrdCodeLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizOrder> list= bizOrderMapper.selectByExample(testExample);
	        PageInfo<BizOrder> pageInfo = new PageInfo<BizOrder>(list);
	        return  pageInfo;
	 }

	 /**
	  * 预订菜品汇总
	  * @param tablepar
	  * @param parmas
	  * @return
	  */
	 public PageInfo<Map<String,Object>>  queryOrdFoodSumByTime(Tablepar tablepar,Map<String,Object> params){
		 PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());

		 List<Map<String,Object>> ordFoodSumList = this.bizOrderMapper.queryOrdFoodSumByTime(params);
		 PageInfo<Map<String,Object>> pageInfo = new PageInfo<Map<String,Object>>(ordFoodSumList);
		 return pageInfo;

	 }

	 public List<Map<String,Object>> queryOrdPrintList(Tablepar tablepar,Map<String,Object> params){
		 //PageHelper.startPage(1, tablepar.getPageSize());
		 List<Map<String,Object>> ordList = this.bizOrderMapper.queryOrdPrintList(params);
		// PageInfo<Map<String,Object>> pageInfo = new PageInfo<Map<String,Object>>(ordList);
		 return ordList;
	 }

	    public List<Map<String,Object>> queryOrdDetailList(Map<String,Object> params){
	    	List<Map<String,Object>> ordDtlList = this.bizOrderMapper.queryOrdDetailList(params);
			 return ordDtlList;
	    }

	  //  获取订单详情
    public BizOrder getDetail(String ordId) {
	     return this.bizOrderMapper.selectDetail(ordId);
    }


	 /**
	 * 分页根据状态
	 * @return
	 */
	 public PageInfo<BizOrder> getOrders(Tablepar tablepar, Integer type, String cardNo, String sellTypeName){
	        BaseEmp baseEmp = baseEmpMapper.selectByCardNo(cardNo);
	        if (baseEmp == null) {
	            return null;
            }
	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizOrder> list= bizOrderMapper.selectOrdersByEmpIdAndStatus(baseEmp.getId(), type, sellTypeName);
	        PageInfo<BizOrder> pageInfo = new PageInfo<BizOrder>(list);
	        return  pageInfo;
	 }

	 public Boolean saveOrder(BizOrder bizOrder) {
         BaseEmp baseEmp = baseEmpMapper.selectByCardNo(bizOrder.getCardNo());
         if (baseEmp == null) {
             return false;
         }
         bizOrder.setTel(baseEmp.getEmpMobile());
         bizOrder.setOrdEmpId(baseEmp.getId());
         bizOrder.setOrdCode(SnowflakeIdWorker.getUUID());
         bizOrder.setId(SnowflakeIdWorker.getUUID());
         bizOrder.setCreateBy(baseEmp.getEmpName());
         bizOrder.setCreateDate(new Date());
         bizOrder.setUpdateBy(baseEmp.getEmpName());
         bizOrder.setUpdateDate(new Date());
         bizOrder.setStatus(1);
         bizOrder.setOrdState(Constants.DINNING_ORD_ORDER);
         bizOrder.setOrdTime(new Date());
         List<BizOrdFood> bizOrdFoodList = bizOrder.getBizOrdFoodList();
         if (bizOrdFoodList.size() == 0) {
             return false;
         }
         BigDecimal ordAmt = new BigDecimal(0);
         for (BizOrdFood bizOrdFood : bizOrdFoodList) {
             BaseFood baseFood = baseFoodMapper.selectByPrimaryKey(bizOrdFood.getFoodId());
             if (baseFood == null) {
                 return false;
             }
             bizOrdFood.setPrice(baseFood.getFoodPrice());
             ordAmt = ordAmt.add(bizOrdFood.getPrice().multiply(bizOrdFood.getQty()));
             bizOrdFood.setId(SnowflakeIdWorker.getUUID());
             bizOrdFood.setOrdId(bizOrder.getId());
         }
         bizOrder.setOrdAmt(ordAmt);
         // 查查看订单标号
		 if (bizOrder.getSellTypeName().equals("外卖")) {
			 Map<String,Object> lastNo = bizOrderMapper.getMaxTakeNo(bizOrder.getPlanedTakeTime());
			if (lastNo == null) {
				bizOrder.setTakeNo(1);
			} else {
				bizOrder.setTakeNo(Integer.valueOf(lastNo.get("take_no").toString()) + 1);
			}
		 }
         bizOrderMapper.insertSelective(bizOrder);
         for (BizOrdFood bizOrdFood : bizOrdFoodList) {
             bizOrdFoodMapper.insertSelective(bizOrdFood);
         }

	     return true;
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			System.out.print(lista);
			BizOrderExample example=new BizOrderExample();
			example.createCriteria().andIdIn(lista);
			return bizOrderMapper.deleteByExample(example);


	}


	@Override
	public BizOrder selectByPrimaryKey(String id) {

			return bizOrderMapper.selectByPrimaryKey(id);

	}


    @Override
    public int updateByPrimaryKeySelective(BizOrder record) {
        return bizOrderMapper.updateByPrimaryKeySelective(record);
    }

    public String updateByPrimaryKeyOrCode(BizOrder record) {
	     if (StringUtils.isNotEmpty(record.getId())) {
			 return updateById(record);
         }

	     return updateByCardNoAndOrdCode(record);
	}

	// 根据卡号和订单编号取餐
	private String updateByCardNoAndOrdCode(BizOrder record) {
		BizOrder bizOrder = bizOrderMapper.selectByOrdCode(record.getOrdCode());
		if (bizOrder == null) {
			return "订单不存在";
		}
		if (!bizOrder.getCardNo().equals(record.getCardNo())) {
			return "卡号错误";
		}
		if ((bizOrder.getSellTypeName().equals("外卖") && !bizOrder.getOrdState().equals(Constants.DINNING_ORD_PAYED))
		|| (bizOrder.getSellTypeName().equals("加班") && !bizOrder.getOrdState().equals(Constants.DINNING_ORD_ORDER))) {
			return "订单状态错误";
		}
		bizOrder.setOrdState(Constants.DINNING_ORD_TAKE);
		bizOrder.setActualTakeTime(DateUtils.format(new Date(), DateUtils.DATE_TIME_PATTERN));
		bizOrder.setUpdateDate(new Date());
		int b =  bizOrderMapper.updateByPrimaryKeySelective(bizOrder);
		if (b <= 0) {
			return "失败";
		}

		return "";
	}

	// 根据ID删除或者取消订单
	private String updateById(BizOrder record) {
		BizOrder bizOrder = bizOrderMapper.selectByPrimaryKey(record.getId());
		if (record.getOrdState()!= null && record.getOrdState().equals(Constants.DINNING_ORD_CANCEL)) {
			if (bizOrder == null || !bizOrder.getOrdState().equals(Constants.DINNING_ORD_ORDER)) {
				return "订单不存在或状态错误";
			}
			bizOrder.setOrdState(Constants.DINNING_ORD_CANCEL);
			bizOrder.setUpdateDate(new Date());
		} else {
			if (bizOrder == null || (!bizOrder.getOrdState().equals(Constants.DINNING_ORD_TAKE) && !bizOrder.getOrdState().equals(Constants.DINNING_ORD_CANCEL))) {
				return "订单不存在或状态错误";
			}
			bizOrder.setStatus(0);
			bizOrder.setUpdateDate(new Date());
		}
		int b =  bizOrderMapper.updateByPrimaryKeySelective(bizOrder);
		if (b <= 0) {
			return "失败";
		}

		return "";
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BizOrder record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return bizOrderMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BizOrder record, BizOrderExample example) {

		return bizOrderMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BizOrder record, BizOrderExample example) {

		return bizOrderMapper.updateByExample(record, example);
	}

	@Override
	public List<BizOrder> selectByExample(BizOrderExample example) {

		return bizOrderMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BizOrderExample example) {

		return bizOrderMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BizOrderExample example) {

		return bizOrderMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param bizOrder
	 * @return
	 */
	public int checkNameUnique(BizOrder bizOrder){
		BizOrderExample example=new BizOrderExample();
		example.createCriteria().andOrdCodeEqualTo(bizOrder.getOrdCode());
		List<BizOrder> list=bizOrderMapper.selectByExample(example);
		return list.size();
	}


}
