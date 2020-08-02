package com.cpdms.service.examination;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.examination.RefExaminationItemMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.examination.RefExaminationItem;
import com.cpdms.model.examination.RefExaminationItemExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 *  RefExaminationItemService
 * @Title: RefExaminationItemService.java 
 * @Package com.cpdms.vote.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-07 10:10:36  
 **/
@Service
public class RefExaminationItemService implements BaseService<RefExaminationItem, RefExaminationItemExample> {
	@Autowired
	private RefExaminationItemMapper refExaminationItemMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<RefExaminationItem> list(Tablepar tablepar, String name){
	        RefExaminationItemExample testExample=new RefExaminationItemExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andExaminationIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<RefExaminationItem> list= refExaminationItemMapper.selectByExample(testExample);
	        PageInfo<RefExaminationItem> pageInfo = new PageInfo<RefExaminationItem>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			RefExaminationItemExample example=new RefExaminationItemExample();
			example.createCriteria().andIdIn(lista);
			return refExaminationItemMapper.deleteByExample(example);


	}


	@Override
	public RefExaminationItem selectByPrimaryKey(String id) {

			return refExaminationItemMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(RefExaminationItem record) {
		return refExaminationItemMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(RefExaminationItem record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return refExaminationItemMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(RefExaminationItem record, RefExaminationItemExample example) {

		return refExaminationItemMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(RefExaminationItem record, RefExaminationItemExample example) {

		return refExaminationItemMapper.updateByExample(record, example);
	}

	@Override
	public List<RefExaminationItem> selectByExample(RefExaminationItemExample example) {

		return refExaminationItemMapper.selectByExample(example);
	}


	@Override
	public long countByExample(RefExaminationItemExample example) {

		return refExaminationItemMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(RefExaminationItemExample example) {

		return refExaminationItemMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param refExaminationItem
	 * @return
	 */
	public int checkNameUnique(RefExaminationItem refExaminationItem){
		RefExaminationItemExample example=new RefExaminationItemExample();
		example.createCriteria().andExaminationIdEqualTo(refExaminationItem.getExaminationId());
		List<RefExaminationItem> list=refExaminationItemMapper.selectByExample(example);
		return list.size();
	}


}
