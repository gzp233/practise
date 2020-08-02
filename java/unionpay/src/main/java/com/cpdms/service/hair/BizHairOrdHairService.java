package com.cpdms.service.hair;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.hair.BizHairOrdHairMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.hair.BizHairOrdHair;
import com.cpdms.model.hair.BizHairOrdHairExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;

/**
 * 美发订单项目表 BizHairOrdHairService
 * @Title: BizHairOrdHairService.java 
 * @Package com.cpdms.hair.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:56:38  
 **/
@Service
public class BizHairOrdHairService implements BaseService<BizHairOrdHair, BizHairOrdHairExample> {
	@Autowired
	private BizHairOrdHairMapper bizHairOrdHairMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BizHairOrdHair> list(Tablepar tablepar, String name){
	        BizHairOrdHairExample testExample=new BizHairOrdHairExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andHairIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizHairOrdHair> list= bizHairOrdHairMapper.selectByExample(testExample);
	        PageInfo<BizHairOrdHair> pageInfo = new PageInfo<BizHairOrdHair>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BizHairOrdHairExample example=new BizHairOrdHairExample();
			example.createCriteria().andIdIn(lista);
			return bizHairOrdHairMapper.deleteByExample(example);


	}


	@Override
	public BizHairOrdHair selectByPrimaryKey(String id) {

			return bizHairOrdHairMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BizHairOrdHair record) {
		return bizHairOrdHairMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BizHairOrdHair record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return bizHairOrdHairMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BizHairOrdHair record, BizHairOrdHairExample example) {

		return bizHairOrdHairMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BizHairOrdHair record, BizHairOrdHairExample example) {

		return bizHairOrdHairMapper.updateByExample(record, example);
	}

	@Override
	public List<BizHairOrdHair> selectByExample(BizHairOrdHairExample example) {

		return bizHairOrdHairMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BizHairOrdHairExample example) {

		return bizHairOrdHairMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BizHairOrdHairExample example) {

		return bizHairOrdHairMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param bizHairOrdHair
	 * @return
	 */
	public int checkNameUnique(BizHairOrdHair bizHairOrdHair){
		BizHairOrdHairExample example=new BizHairOrdHairExample();
		example.createCriteria().andHairIdEqualTo(bizHairOrdHair.getHairId());
		List<BizHairOrdHair> list=bizHairOrdHairMapper.selectByExample(example);
		return list.size();
	}


}
