package com.cpdms.service.hair;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.hair.BaseHairMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.hair.BaseHair;
import com.cpdms.model.hair.BaseHairExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;

/**
 * 美发项目表 BaseHairService
 * @Title: BaseHairService.java 
 * @Package com.cpdms.hair.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:57:06  
 **/
@Service
public class BaseHairService implements BaseService<BaseHair, BaseHairExample> {
	@Autowired
	private BaseHairMapper baseHairMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseHair> list(Tablepar tablepar, String name){
	        BaseHairExample testExample=new BaseHairExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andHairNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseHair> list= baseHairMapper.selectByExample(testExample);
	        PageInfo<BaseHair> pageInfo = new PageInfo<BaseHair>(list);
	        return  pageInfo;
	 }

	 public PageInfo<BaseHair> getHairAndImgs(Tablepar tablepar, BaseHair baseHair) {
	     PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
        List<BaseHair> list= baseHairMapper.selectHairAndImgs(baseHair);
        PageInfo<BaseHair> pageInfo = new PageInfo<BaseHair>(list);
        return  pageInfo;
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BaseHairExample example=new BaseHairExample();
			example.createCriteria().andIdIn(lista);
			return baseHairMapper.deleteByExample(example);


	}


	@Override
	public BaseHair selectByPrimaryKey(String id) {

			return baseHairMapper.selectByPrimaryKey(id);

	}

	public BaseHair selectOne(String id) {

			return baseHairMapper.selectOne(id);

	}

	@Override
	public int updateByPrimaryKeySelective(BaseHair record) {
		return baseHairMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseHair record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseHairMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseHair record, BaseHairExample example) {

		return baseHairMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseHair record, BaseHairExample example) {

		return baseHairMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseHair> selectByExample(BaseHairExample example) {

		return baseHairMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseHairExample example) {

		return baseHairMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseHairExample example) {

		return baseHairMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseHair
	 * @return
	 */
	public int checkNameUnique(BaseHair baseHair){
		BaseHairExample example=new BaseHairExample();
		example.createCriteria().andHairNameEqualTo(baseHair.getHairName());
		List<BaseHair> list=baseHairMapper.selectByExample(example);
		return list.size();
	}


}
