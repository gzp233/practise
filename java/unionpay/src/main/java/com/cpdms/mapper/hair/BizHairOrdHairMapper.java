package com.cpdms.mapper.hair;

import com.cpdms.model.hair.BizHairOrdHair;
import com.cpdms.model.hair.BizHairOrdHairExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 美发订单项目表 BizHairOrdHairMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:56:38
 */
public interface BizHairOrdHairMapper {

    long countByExample(BizHairOrdHairExample example);

    int deleteByExample(BizHairOrdHairExample example);

    int deleteByPrimaryKey(String id);

    int insert(BizHairOrdHair record);

    int insertSelective(BizHairOrdHair record);

    List<BizHairOrdHair> selectByExample(BizHairOrdHairExample example);

    BizHairOrdHair selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BizHairOrdHair record, @Param("example") BizHairOrdHairExample example);

    int updateByExample(@Param("record") BizHairOrdHair record, @Param("example") BizHairOrdHairExample example);

    int updateByPrimaryKeySelective(BizHairOrdHair record);

    int updateByPrimaryKey(BizHairOrdHair record);

}
