package com.cpdms.mapper.payment;

import java.util.List;

import com.cpdms.model.payment.BizPayment;
import com.cpdms.model.payment.BizPaymentExample;
import org.apache.ibatis.annotations.Param;

/**
 * 交易记录表 BizPaymentMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-27 11:06:26
 */
public interface BizPaymentMapper {
      	   	      	      	      	      	      	      	      	      	      
    long countByExample(BizPaymentExample example);

    int deleteByExample(BizPaymentExample example);
		
    int deleteByPrimaryKey(String id);
		
    int insert(BizPayment record);

    int insertSelective(BizPayment record);

    List<BizPayment> selectByExample(BizPaymentExample example);
		
    BizPayment selectByPrimaryKey(String id);
		
    int updateByExampleSelective(@Param("record") BizPayment record, @Param("example") BizPaymentExample example);

    int updateByExample(@Param("record") BizPayment record, @Param("example") BizPaymentExample example); 
		
    int updateByPrimaryKeySelective(BizPayment record);

    int updateByPrimaryKey(BizPayment record);
  	  	
}