package com.cpdms.mapper.dinning;

import java.util.List;

import com.cpdms.model.dinning.BizOrdPay;
import com.cpdms.model.dinning.BizOrdPayExample;
import org.apache.ibatis.annotations.Param;

/**
 * 订单支付 BizOrdPayMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-27 11:04:59
 */
public interface BizOrdPayMapper {

    long countByExample(BizOrdPayExample example);

    int deleteByExample(BizOrdPayExample example);

    int deleteByPrimaryKey(String id);

    int insert(BizOrdPay record);

    int insertSelective(BizOrdPay record);

    List<BizOrdPay> selectByExample(BizOrdPayExample example);

    BizOrdPay selectByPrimaryKey(String id);

    BizOrdPay selectByOrdId(String ordId);

    int updateByExampleSelective(@Param("record") BizOrdPay record, @Param("example") BizOrdPayExample example);

    int updateByExample(@Param("record") BizOrdPay record, @Param("example") BizOrdPayExample example);

    int updateByPrimaryKeySelective(BizOrdPay record);

    int updateByPrimaryKey(BizOrdPay record);

}
